<?php

declare(strict_types=1);

namespace Tests\Unit\Mapping\Configuration;

use ChamberOrchestra\DoctrineClockBundle\Mapping\Configuration\TimestampConfiguration;
use ChamberOrchestra\MetadataBundle\Mapping\ORM\AbstractMetadataConfiguration;
use PHPUnit\Framework\TestCase;

final class TimestampConfigurationTest extends TestCase
{
    public function testItExtendsAbstractMetadataConfiguration(): void
    {
        $config = new TimestampConfiguration();

        self::assertInstanceOf(AbstractMetadataConfiguration::class, $config);
    }

    public function testEmptyConfigurationReturnsNoFields(): void
    {
        $config = new TimestampConfiguration();

        self::assertSame([], $config->getCreateFields());
        self::assertSame([], $config->getUpdateFields());
    }

    public function testSingleCreateField(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('createdAt', ['type' => 'create']);

        self::assertSame(['createdAt'], $config->getCreateFields());
        self::assertSame([], $config->getUpdateFields());
    }

    public function testSingleUpdateField(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('updatedAt', ['type' => 'update']);

        self::assertSame([], $config->getCreateFields());
        self::assertSame(['updatedAt'], $config->getUpdateFields());
    }

    public function testMixedCreateAndUpdateFields(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('createdAt', ['type' => 'create']);
        $config->mapField('updatedAt', ['type' => 'update']);

        self::assertSame(['createdAt'], $config->getCreateFields());
        self::assertSame(['updatedAt'], $config->getUpdateFields());
    }

    public function testMultipleCreateFields(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('createdAt', ['type' => 'create']);
        $config->mapField('registeredAt', ['type' => 'create']);

        self::assertSame(['createdAt', 'registeredAt'], $config->getCreateFields());
    }

    public function testMultipleUpdateFields(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('updatedAt', ['type' => 'update']);
        $config->mapField('modifiedAt', ['type' => 'update']);

        self::assertSame(['updatedAt', 'modifiedAt'], $config->getUpdateFields());
    }

    public function testSerializationPreservesState(): void
    {
        $config = new TimestampConfiguration();
        $config->mapField('createdAt', ['type' => 'create']);
        $config->mapField('updatedAt', ['type' => 'update']);

        /** @var TimestampConfiguration $restored */
        $restored = \unserialize(\serialize($config));

        self::assertSame(['createdAt'], $restored->getCreateFields());
        self::assertSame(['updatedAt'], $restored->getUpdateFields());
    }
}
