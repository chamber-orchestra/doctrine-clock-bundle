<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Mapping\Driver;

use ChamberOrchestra\DoctrineClockBundle\Exception\MappingException;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\CreateTimestamp;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\UpdateTimestamp;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Configuration\TimestampConfiguration;
use ChamberOrchestra\MetadataBundle\Mapping\Driver\AbstractMappingDriver;
use ChamberOrchestra\MetadataBundle\Mapping\ExtensionMetadataInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class TimestampDriver extends AbstractMappingDriver
{
    private const array TIMESTAMP_ATTRIBUTES = [
        CreateTimestamp::class => 'create',
        UpdateTimestamp::class => 'update',
    ];

    public function supports(ExtensionMetadataInterface $metadata): bool
    {
        $reflection = $metadata->getOriginMetadata()->getReflectionClass();

        foreach ($reflection->getProperties() as $property) {
            foreach (self::TIMESTAMP_ATTRIBUTES as $attributeClass => $_) {
                if (null !== $this->reader->getPropertyAttribute($property, $attributeClass)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function loadMetadataForClass(ExtensionMetadataInterface $extensionMetadata): void
    {
        /** @var ClassMetadata<object> $meta */
        $meta = $extensionMetadata->getOriginMetadata();
        $class = $meta->getReflectionClass();
        $fieldNames = $meta->getFieldNames();

        $config = new TimestampConfiguration();
        $hasMapping = false;

        foreach ($class->getProperties() as $property) {
            $propertyName = $property->getName();

            foreach (self::TIMESTAMP_ATTRIBUTES as $attributeClass => $type) {
                if (null === $this->reader->getPropertyAttribute($property, $attributeClass)) {
                    continue;
                }

                if (!\in_array($propertyName, $fieldNames, true)) {
                    throw MappingException::unmappedTimestampField($meta->getName(), $propertyName, $attributeClass);
                }

                $config->mapField($propertyName, ['type' => $type]);
                $hasMapping = true;
            }
        }

        if ($hasMapping) {
            $extensionMetadata->addConfiguration($config);
        }
    }

    protected function getPropertyAttribute(): ?string
    {
        return null;
    }
}
