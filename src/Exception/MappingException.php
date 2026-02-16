<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Exception;

class MappingException extends \ChamberOrchestra\MetadataBundle\Exception\MappingException
{
    public static function unmappedTimestampField(string $className, string $field, string $attributeClass): self
    {
        $shortName = \substr($attributeClass, (int) \strrpos($attributeClass, '\\') + 1);

        return new self(\sprintf('Property "%s" in "%s" has #[%s] but is not a Doctrine-mapped field.', $field, $className, $shortName));
    }
}
