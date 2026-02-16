<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Type;

use ChamberOrchestra\DoctrineClockBundle\Type\Exception\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;

class DateTimeType extends \Doctrine\DBAL\Types\DateTimeType
{
    use DateTimeTrait;

    public function getName(): string
    {
        return Types::DATETIME_MUTABLE;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($this->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTime
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        if (!\is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', \DateTime::class]);
        }

        $val = \DateTime::createFromFormat($this->getDateTimeFormatString(), $value);
        if (false === $val) {
            $val = \date_create($value);
        }

        if (false === $val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $this->getDateTimeFormatString());
        }

        return $val;
    }
}
