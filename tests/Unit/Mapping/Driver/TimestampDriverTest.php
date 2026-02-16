<?php

declare(strict_types=1);

namespace Tests\Unit\Mapping\Driver;

use ChamberOrchestra\DoctrineClockBundle\Exception\MappingException;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\CreateTimestamp;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\UpdateTimestamp;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Configuration\TimestampConfiguration;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Driver\TimestampDriver;
use ChamberOrchestra\MetadataBundle\Mapping\Driver\MappingDriverInterface;
use ChamberOrchestra\MetadataBundle\Mapping\ExtensionMetadataInterface;
use ChamberOrchestra\MetadataBundle\Reader\AttributeReader;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

final class TimestampDriverTest extends TestCase
{
    private TimestampDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new TimestampDriver(new AttributeReader());
    }

    public function testItImplementsMappingDriverInterface(): void
    {
        self::assertInstanceOf(MappingDriverInterface::class, $this->driver);
    }

    public function testSupportsReturnsTrueForEntityWithCreateTimestamp(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithCreateTimestamp::class);

        self::assertTrue($this->driver->supports($metadata));
    }

    public function testSupportsReturnsTrueForEntityWithUpdateTimestamp(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithUpdateTimestamp::class);

        self::assertTrue($this->driver->supports($metadata));
    }

    public function testSupportsReturnsTrueForEntityWithBothTimestamps(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithBothTimestamps::class);

        self::assertTrue($this->driver->supports($metadata));
    }

    public function testSupportsReturnsFalseForEntityWithoutTimestamps(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithoutTimestamps::class);

        self::assertFalse($this->driver->supports($metadata));
    }

    public function testLoadMetadataForCreateTimestamp(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithCreateTimestamp::class);

        $this->driver->loadMetadataForClass($metadata);

        $config = $metadata->getConfiguration(TimestampConfiguration::class);
        self::assertInstanceOf(TimestampConfiguration::class, $config);
        self::assertSame(['createdAt'], $config->getCreateFields());
        self::assertSame([], $config->getUpdateFields());
    }

    public function testLoadMetadataForUpdateTimestamp(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithUpdateTimestamp::class);

        $this->driver->loadMetadataForClass($metadata);

        $config = $metadata->getConfiguration(TimestampConfiguration::class);
        self::assertInstanceOf(TimestampConfiguration::class, $config);
        self::assertSame([], $config->getCreateFields());
        self::assertSame(['updatedAt'], $config->getUpdateFields());
    }

    public function testLoadMetadataForBothTimestamps(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithBothTimestamps::class);

        $this->driver->loadMetadataForClass($metadata);

        $config = $metadata->getConfiguration(TimestampConfiguration::class);
        self::assertInstanceOf(TimestampConfiguration::class, $config);
        self::assertSame(['createdAt'], $config->getCreateFields());
        self::assertSame(['updatedAt'], $config->getUpdateFields());
    }

    public function testLoadMetadataSkipsEntityWithoutTimestamps(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithoutTimestamps::class);

        $this->driver->loadMetadataForClass($metadata);

        self::assertNull($metadata->getConfiguration(TimestampConfiguration::class));
    }

    public function testLoadMetadataThrowsForUnmappedField(): void
    {
        $metadata = $this->createExtensionMetadata(EntityWithUnmappedTimestamp::class);

        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('is not a Doctrine-mapped field');

        $this->driver->loadMetadataForClass($metadata);
    }

    private function createExtensionMetadata(string $className): ExtensionMetadataInterface
    {
        $classMetadata = new ClassMetadata($className);
        $classMetadata->initializeReflection(new \Doctrine\Persistence\Mapping\RuntimeReflectionService());
        $classMetadata->mapField(['fieldName' => 'id', 'type' => 'integer']);

        if (\property_exists($className, 'createdAt') && $this->isDoctrineColumn($className, 'createdAt')) {
            $classMetadata->mapField(['fieldName' => 'createdAt', 'type' => 'datetime_immutable']);
        }
        if (\property_exists($className, 'updatedAt') && $this->isDoctrineColumn($className, 'updatedAt')) {
            $classMetadata->mapField(['fieldName' => 'updatedAt', 'type' => 'datetime_immutable']);
        }

        return new \ChamberOrchestra\MetadataBundle\Mapping\ORM\ExtensionMetadata($classMetadata);
    }

    private function isDoctrineColumn(string $className, string $property): bool
    {
        $ref = new \ReflectionProperty($className, $property);
        foreach ($ref->getAttributes() as $attr) {
            if (ORM\Column::class === $attr->getName()) {
                return true;
            }
        }

        return false;
    }
}

/**
 * @internal
 */
#[ORM\Entity]
class EntityWithCreateTimestamp
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[CreateTimestamp]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DatePoint $createdAt = null;
}

/**
 * @internal
 */
#[ORM\Entity]
class EntityWithUpdateTimestamp
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[UpdateTimestamp]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DatePoint $updatedAt = null;
}

/**
 * @internal
 */
#[ORM\Entity]
class EntityWithBothTimestamps
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[CreateTimestamp]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DatePoint $createdAt = null;

    #[UpdateTimestamp]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DatePoint $updatedAt = null;
}

/**
 * @internal
 */
#[ORM\Entity]
class EntityWithoutTimestamps
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name = '';
}

/**
 * @internal
 */
#[ORM\Entity]
class EntityWithUnmappedTimestamp
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[CreateTimestamp]
    private ?DatePoint $createdAt = null;
}
