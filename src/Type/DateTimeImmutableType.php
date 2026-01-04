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
use DateTimeImmutable;
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

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|null
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->format($this->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTimeImmutable::class]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if (null === $value || $value instanceof DatePoint) {
            return $value;
        }

        if (!$dateTime = DatePoint::createFromFormat($this->getDateTimeFormatString(), $value)) {
            $dateTime = \date_create_immutable($value);
        }

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $this->getDateTimeFormatString());
        }

        return $dateTime;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
