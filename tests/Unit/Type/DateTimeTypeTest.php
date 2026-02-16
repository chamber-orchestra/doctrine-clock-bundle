<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\DoctrineClockBundle\Type\DateTimeType;
use ChamberOrchestra\DoctrineClockBundle\Type\Exception\ConversionException;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;

final class DateTimeTypeTest extends TestCase
{
    private DateTimeType $type;

    protected function setUp(): void
    {
        $this->type = new DateTimeType();
    }

    public function testGetName(): void
    {
        self::assertSame(Types::DATETIME_MUTABLE, $this->type->getName());
    }

    public function testConvertToDatabaseValueReturnsNull(): void
    {
        $platform = new PostgreSQLPlatform();

        self::assertNull($this->type->convertToDatabaseValue(null, $platform));
    }

    public function testConvertToDatabaseValueFormatsDateTime(): void
    {
        $platform = new PostgreSQLPlatform();
        $value = new \DateTime('2024-01-01 10:11:12');

        self::assertSame('2024-01-01 10:11:12.000000', $this->type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertToDatabaseValuePreservesMicroseconds(): void
    {
        $platform = new PostgreSQLPlatform();
        $value = new \DateTime('2024-01-01 10:11:12.654321');

        self::assertSame('2024-01-01 10:11:12.654321', $this->type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertToDatabaseValueRejectsInvalidType(): void
    {
        $this->expectException(ConversionException::class);

        $platform = new PostgreSQLPlatform();
        $this->type->convertToDatabaseValue('invalid', $platform);
    }

    public function testConvertToPHPValueReturnsNullForNull(): void
    {
        $platform = new PostgreSQLPlatform();

        self::assertNull($this->type->convertToPHPValue(null, $platform));
    }

    public function testConvertToPHPValuePassesThroughDateTime(): void
    {
        $platform = new PostgreSQLPlatform();
        $value = new \DateTime('2024-01-01 10:11:12');

        self::assertSame($value, $this->type->convertToPHPValue($value, $platform));
    }

    public function testConvertToPHPValueParsesString(): void
    {
        $platform = new PostgreSQLPlatform();

        $value = $this->type->convertToPHPValue('2024-01-01 10:11:12.000000', $platform);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('2024-01-01 10:11:12.000000', $value->format('Y-m-d H:i:s.u'));
    }

    public function testConvertToPHPValuePreservesMicroseconds(): void
    {
        $platform = new PostgreSQLPlatform();

        $value = $this->type->convertToPHPValue('2024-01-01 10:11:12.654321', $platform);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('654321', $value->format('u'));
    }

    public function testConvertToPHPValueRejectsNonString(): void
    {
        $this->expectException(ConversionException::class);

        $platform = new PostgreSQLPlatform();
        $this->type->convertToPHPValue(12345, $platform);
    }

    public function testConvertToPHPValueThrowsOnInvalidDate(): void
    {
        $this->expectException(ConversionException::class);

        $platform = new PostgreSQLPlatform();
        $this->type->convertToPHPValue('not-a-date', $platform);
    }
}
