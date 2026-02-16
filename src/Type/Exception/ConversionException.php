<?php

declare(strict_types=1);

namespace ChamberOrchestra\DoctrineClockBundle\Type\Exception;

class ConversionException extends \Doctrine\DBAL\Types\ConversionException
{
    public static function conversionFailed(mixed $value, string $toType): self
    {
        $value = self::stringifyValue($value);

        return new self('Could not convert database value "'.$value.'" to Doctrine Type '.$toType);
    }

    public static function conversionFailedFormat(mixed $value, string $toType, string $expectedFormat): self
    {
        $value = self::stringifyValue($value);

        return new self(
            'Could not convert database value "'.$value.'" to Doctrine Type '.
            $toType.'. Expected format: '.$expectedFormat
        );
    }

    /**
     * @param list<string> $expectedTypes
     */
    public static function conversionFailedInvalidType(mixed $value, string $toType, array $expectedTypes = []): self
    {
        $value = self::stringifyValue($value);

        return new self(
            'Could not convert database value "'.$value.'" to Doctrine Type '.
            $toType.'. Expected types: '.\implode(', ', $expectedTypes)
        );
    }

    private static function stringifyValue(mixed $value): string
    {
        if (null === $value) {
            $stringValue = 'null';
        } elseif (\is_object($value)) {
            try {
                $stringValue = \method_exists($value, '__toString')
                    ? (string) $value
                    : \get_debug_type($value);
            } catch (\Throwable) {
                $stringValue = \get_debug_type($value);
            }
        } elseif (\is_array($value)) {
            $stringValue = 'array';
        } elseif (\is_resource($value)) {
            $stringValue = 'resource';
        } elseif (\is_scalar($value)) {
            $stringValue = (string) $value;
        } else {
            $stringValue = \get_debug_type($value);
        }

        return (\strlen($stringValue) > 32) ? \substr($stringValue, 0, 20).'...' : $stringValue;
    }
}
