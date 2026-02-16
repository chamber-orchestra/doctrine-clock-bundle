<?php

declare(strict_types=1);

namespace Tests\Integrational;

use Symfony\Component\Clock\DatePoint;
use Tests\Fixtures\Entity\CreateOnlyEntity;
use Tests\Fixtures\Entity\PrecisedManualTimestampEntity;
use Tests\Fixtures\Entity\PrecisedTimestampedEntity;
use Tests\Fixtures\Entity\TimestampedEntity;
use Tests\Fixtures\Entity\UpdateOnlyEntity;

final class TimestampSubscriberTest extends DoctrineTestCase
{
    public function testPersistSetsCreatedAndUpdatedForTimestampedEntity(): void
    {
        $entity = new TimestampedEntity('first');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertInstanceOf(DatePoint::class, $entity->getCreatedDatetime());
        self::assertInstanceOf(DatePoint::class, $entity->getUpdatedDatetime());
        self::assertSame('2024-01-01 00:00:00.000000', $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u'));
        self::assertSame('2024-01-01 00:00:00.000000', $entity->getUpdatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testUpdateRefreshesUpdatedDatetime(): void
    {
        $entity = new TimestampedEntity('first');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $firstUpdated = $entity->getUpdatedDatetime();

        $entity->setName('second');
        $this->getClock()->sleep(1);
        $this->entityManager->flush();

        $secondUpdated = $entity->getUpdatedDatetime();

        self::assertNotSame($firstUpdated, $secondUpdated);
        self::assertSame('2024-01-01 00:00:01.000000', $secondUpdated->format('Y-m-d H:i:s.u'));
    }

    public function testPersistSetsCreatedOnlyForCreateInterface(): void
    {
        $entity = new CreateOnlyEntity('created');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertInstanceOf(DatePoint::class, $entity->getCreatedDatetime());
        self::assertSame('2024-01-01 00:00:00.000000', $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testPersistSetsUpdatedOnlyForUpdateInterface(): void
    {
        $entity = new UpdateOnlyEntity('updated');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertInstanceOf(DatePoint::class, $entity->getUpdatedDatetime());
        self::assertSame('2024-01-01 00:00:00.000000', $entity->getUpdatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testManuallySetTimestampsAreNotOverwritten(): void
    {
        $manualDate = new DatePoint('2020-06-15 12:30:00');

        $entity = new TimestampedEntity('manual');
        $entity->setCreatedDatetime($manualDate);
        $entity->setUpdatedDatetime($manualDate);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertSame('2020-06-15 12:30:00.000000', $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u'));
        self::assertSame('2020-06-15 12:30:00.000000', $entity->getUpdatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testUpdateDoesNotChangeCreatedDatetime(): void
    {
        $entity = new TimestampedEntity('first');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $createdBefore = $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u');

        $entity->setName('second');
        $this->getClock()->sleep(1);
        $this->entityManager->flush();

        self::assertSame($createdBefore, $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testPrecisedTimestampedEntitySetsTimestamps(): void
    {
        $entity = new PrecisedTimestampedEntity('precised');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertInstanceOf(DatePoint::class, $entity->getCreatedDatetime());
        self::assertInstanceOf(DatePoint::class, $entity->getUpdatedDatetime());
    }

    public function testPrecisedTimestampedEntityUpdateRefreshesUpdated(): void
    {
        $entity = new PrecisedTimestampedEntity('precised');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $createdBefore = $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u');

        $entity->setName('changed');
        $this->getClock()->sleep(1);
        $this->entityManager->flush();

        self::assertSame($createdBefore, $entity->getCreatedDatetime()->format('Y-m-d H:i:s.u'));
        self::assertSame('2024-01-01 00:00:01.000000', $entity->getUpdatedDatetime()->format('Y-m-d H:i:s.u'));
    }

    public function testEntityWithoutTimestampAttributesIsIgnored(): void
    {
        $created = new DatePoint('2024-01-01 00:00:00');
        $updated = new DatePoint('2024-01-01 00:00:00');
        $entity = new PrecisedManualTimestampEntity($created, $updated);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        // Timestamps remain exactly as set — the subscriber does not touch this entity
        self::assertSame($created, $entity->getCreatedDatetime());
        self::assertSame($updated, $entity->getUpdatedDatetime());
    }
}
