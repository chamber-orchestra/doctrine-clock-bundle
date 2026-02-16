<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Mapping\Configuration;

use ChamberOrchestra\MetadataBundle\Mapping\ORM\AbstractMetadataConfiguration;

class TimestampConfiguration extends AbstractMetadataConfiguration
{
    /**
     * @return list<string>
     */
    public function getCreateFields(): array
    {
        $fields = [];
        foreach ($this->mappings as $fieldName => $mapping) {
            if ('create' === $mapping['type']) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }

    /**
     * @return list<string>
     */
    public function getUpdateFields(): array
    {
        $fields = [];
        foreach ($this->mappings as $fieldName => $mapping) {
            if ('update' === $mapping['type']) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }
}
