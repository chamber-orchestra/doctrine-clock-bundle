<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Exception;

use ChamberOrchestra\DoctrineClockBundle\Type\Exception\ConversionException;
use PHPUnit\Framework\TestCase;

final class ConversionExceptionTest extends TestCase
{
    public function testConversionFailedTruncatesValue(): void
    {
        $value = \str_repeat('a', 40);
        $exception = ConversionException::conversionFailed($value, 'custom');

        self::assertStringContainsString('aaaaaaaaaaaaaaaaaaaa...', $exception->getMessage());
    }

    public function testConversionFailedFormatIncludesExpectedFormat(): void
    {
        $exception = ConversionException::conversionFailedFormat('2024-01-01', 'custom', 'Y-m-d');

        self::assertStringContainsString('Expected format: Y-m-d', $exception->getMessage());
    }

    public function testConversionFailedInvalidTypeListsExpectedTypes(): void
    {
        $exception = ConversionException::conversionFailedInvalidType('value', 'custom', ['null', 'DateTime']);

        self::assertStringContainsString('Expected types: null, DateTime', $exception->getMessage());
    }

    public function testStringifyValueHandlesObjectWithThrowingToString(): void
    {
        $object = new class {
            public function __toString(): string
            {
                throw new \RuntimeException('broken');
            }
        };

        $exception = ConversionException::conversionFailed($object, 'custom');

        self::assertStringContainsString('class@anonymous', $exception->getMessage());
    }

    public function testStringifyValueHandlesNull(): void
    {
        $exception = ConversionException::conversionFailed(null, 'custom');

        self::assertStringContainsString('null', $exception->getMessage());
    }

    public function testStringifyValueHandlesArray(): void
    {
        $exception = ConversionException::conversionFailed([1, 2, 3], 'custom');

        self::assertStringContainsString('array', $exception->getMessage());
    }
}
