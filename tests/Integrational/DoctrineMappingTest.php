<?php

declare(strict_types=1);

namespace Tests\Integrational;

use Symfony\Bridge\Doctrine\Types\DatePointType;
use Tests\Fixtures\Entity\PrecisedTimestampedEntity;
use Tests\Fixtures\Entity\TimestampedEntity;

final class DoctrineMappingTest extends DoctrineTestCase
{
    public function testTimestampedEntityMappings(): void
    {
        $metadata = $this->entityManager->getClassMetadata(TimestampedEntity::class);
        $createdMapping = $metadata->getFieldMapping('createdDatetime');
        $updatedMapping = $metadata->getFieldMapping('updatedDatetime');

        self::assertSame(DatePointType::NAME, $createdMapping['type']);
        self::assertSame(DatePointType::NAME, $updatedMapping['type']);
    }

    public function testPrecisedTimestampedEntityMappings(): void
    {
        $metadata = $this->entityManager->getClassMetadata(PrecisedTimestampedEntity::class);
        $createdMapping = $metadata->getFieldMapping('createdDatetime');
        $updatedMapping = $metadata->getFieldMapping('updatedDatetime');

        self::assertSame(6, $createdMapping['scale']);
        self::assertSame(6, $updatedMapping['scale']);
    }
}
