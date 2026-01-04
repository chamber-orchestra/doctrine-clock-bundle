<?php

namespace ChamberOrchestra\DoctrineClockBundle\Type\Exception;

class ConversionException extends \Doctrine\DBAL\Types\ConversionException
{
    static public function conversionFailed(mixed $value, string $toType): self
    {
        $value = self::stringifyValue($value);

        return new self('Could not convert database value "'.$value.'" to Doctrine Type '.$toType);
    }

    static public function conversionFailedFormat(mixed $value, string $toType, string $expectedFormat): self
    {
        $value = self::stringifyValue($value);

        return new self(
            'Could not convert database value "'.$value.'" to Doctrine Type '.
            $toType.'. Expected format: '.$expectedFormat
        );
    }

    static public function conversionFailedInvalidType(mixed $value, string $toType, array $expectedTypes = []): self
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
            if (\method_exists($value, '__toString')) {
                $stringValue = (string)$value;
            } else {
                $stringValue = \get_debug_type($value);
            }
        } elseif (\is_array($value)) {
            $stringValue = 'array';
        } elseif (\is_resource($value)) {
            $stringValue = 'resource';
        } else {
            $stringValue = (string)$value;
        }

        return (\strlen($stringValue) > 32) ? \substr($stringValue, 0, 20).'...' : $stringValue;
    }
}
