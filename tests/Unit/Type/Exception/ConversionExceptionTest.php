<?php

declare(strict_types=1);

namespace Tests\Unit\Type\Exception;

use ChamberOrchestra\DoctrineClockBundle\Type\Exception\ConversionException;
use PHPUnit\Framework\TestCase;

final class ConversionExceptionTest extends TestCase
{
    public function testConversionFailedTruncatesValue(): void
    {
        $value = str_repeat('a', 40);
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
}
