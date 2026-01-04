<?php

declare(strict_types=1);

namespace Tests\Integrational;

use Symfony\Component\Clock\DatePoint;
use Tests\Fixtures\Entity\PrecisedManualTimestampEntity;

final class PrecisedDatePointRoundTripTest extends DoctrineTestCase
{
    public function testDatePointPrecisionIsPreserved(): void
    {
        $created = new DatePoint('2024-01-01 10:11:12.123456');
        $updated = new DatePoint('2024-01-01 10:11:12.654321');
        $entity = new PrecisedManualTimestampEntity($created, $updated);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $id = $entity->getId();

        $this->entityManager->clear();

        $reloaded = $this->entityManager->find(PrecisedManualTimestampEntity::class, $id);

        self::assertInstanceOf(PrecisedManualTimestampEntity::class, $reloaded);
        self::assertSame('2024-01-01 10:11:12', $reloaded->getCreatedDatetime()->format('Y-m-d H:i:s'));
        self::assertSame('2024-01-01 10:11:12', $reloaded->getUpdatedDatetime()->format('Y-m-d H:i:s'));
        self::assertSame(6, \strlen($reloaded->getCreatedDatetime()->format('u')));
        self::assertSame(6, \strlen($reloaded->getUpdatedDatetime()->format('u')));
    }
}
