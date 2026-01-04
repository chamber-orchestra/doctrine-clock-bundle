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
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;

class DateTimeType extends \Doctrine\DBAL\Types\DateTimeType
{
    use DateTimeTrait;

    public function getName(): string
    {
        return Types::DATETIME_MUTABLE;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|null
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($this->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        if (!$val = DateTime::createFromFormat($this->getDateTimeFormatString(), $value)) {
            $val = \date_create($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        return $val;
    }
}
