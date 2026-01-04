<?php

declare(strict_types=1);

namespace Tests\Integrational;

use DateTime;
use DateTimeImmutable;
use Tests\Fixtures\Entity\CustomTypesEntity;

final class CustomTypesRoundTripTest extends DoctrineTestCase
{
    public function testCustomDbalTypesRoundTrip(): void
    {
        $mutable = new DateTime('2024-01-01 10:11:12.654321');
        $immutable = new DateTimeImmutable('2024-01-01 10:11:12.123456');
        $entity = new CustomTypesEntity($mutable, $immutable, '10.50');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $id = $entity->getId();

        $this->entityManager->clear();

        $reloaded = $this->entityManager->find(CustomTypesEntity::class, $id);

        self::assertInstanceOf(CustomTypesEntity::class, $reloaded);
        self::assertSame('2024-01-01 10:11:12.654321', $reloaded->getDateMutable()->format('Y-m-d H:i:s.u'));
        self::assertSame('2024-01-01 10:11:12.123456', $reloaded->getDateImmutable()->format('Y-m-d H:i:s.u'));
        self::assertSame('10.5', $reloaded->getAmount());
    }
}
