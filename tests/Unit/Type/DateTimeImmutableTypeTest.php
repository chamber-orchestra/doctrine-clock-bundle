<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\DoctrineClockBundle\Type\DateTimeImmutableType;
use ChamberOrchestra\DoctrineClockBundle\Type\Exception\ConversionException;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

final class DateTimeImmutableTypeTest extends TestCase
{
    private DateTimeImmutableType $type;

    protected function setUp(): void
    {
        $this->type = new DateTimeImmutableType();
    }

    public function testGetName(): void
    {
        self::assertSame(Types::DATETIME_IMMUTABLE, $this->type->getName());
    }

    public function testConvertToDatabaseValueReturnsNull(): void
    {
        $platform = new PostgreSQLPlatform();

        self::assertNull($this->type->convertToDatabaseValue(null, $platform));
    }

    public function testConvertToDatabaseValueFormatsDateTime(): void
    {
        $platform = new PostgreSQLPlatform();
        $value = new DateTimeImmutable('2024-01-01 10:11:12');

        self::assertSame('2024-01-01 10:11:12.000000', $this->type->convertToDatabaseValue($value, $platform));
    }

    public function testConvertToDatabaseValueRejectsInvalidType(): void
    {
        $this->expectException(ConversionException::class);

        $platform = new PostgreSQLPlatform();
        $this->type->convertToDatabaseValue('invalid', $platform);
    }

    public function testConvertToPHPValueAcceptsDatePoint(): void
    {
        $platform = new PostgreSQLPlatform();
        $value = new DatePoint('2024-01-01 10:11:12');

        self::assertSame($value, $this->type->convertToPHPValue($value, $platform));
    }

    public function testConvertToPHPValueParsesString(): void
    {
        $platform = new PostgreSQLPlatform();

        $value = $this->type->convertToPHPValue('2024-01-01 10:11:12.000000', $platform);

        self::assertInstanceOf(DateTimeImmutable::class, $value);
        self::assertSame('2024-01-01 10:11:12.000000', $value->format('Y-m-d H:i:s.u'));
    }

    public function testRequiresSqlCommentHint(): void
    {
        $platform = new PostgreSQLPlatform();

        self::assertTrue($this->type->requiresSQLCommentHint($platform));
    }
}
