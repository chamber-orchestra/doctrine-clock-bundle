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
use Symfony\Component\Clock\DatePoint;

class DateTimeImmutableType extends \Doctrine\DBAL\Types\DateTimeImmutableType
{
    use DateTimeTrait;

    public function getName(): string
    {
        return Types::DATETIME_IMMUTABLE;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($this->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', \DateTimeImmutable::class]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DatePoint
    {
        if (null === $value || $value instanceof DatePoint) {
            return $value;
        }

        if (!\is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', DatePoint::class]);
        }

        try {
            return DatePoint::createFromFormat($this->getDateTimeFormatString(), $value);
        } catch (\DateMalformedStringException) {
            // Fall back to generic date parsing
        }

        $dateTime = \date_create_immutable($value);
        if (false === $dateTime) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $this->getDateTimeFormatString());
        }

        return DatePoint::createFromInterface($dateTime);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
