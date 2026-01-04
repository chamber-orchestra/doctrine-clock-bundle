<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use ChamberOrchestra\DoctrineClockBundle\Type\DecimalType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;

final class DecimalTypeTest extends TestCase
{
    public function testConvertToPHPValueCastsToString(): void
    {
        $type = new DecimalType();
        $platform = new PostgreSQLPlatform();

        self::assertSame('10.50', $type->convertToPHPValue('10.50', $platform));
    }

    public function testConvertToPHPValueKeepsNull(): void
    {
        $type = new DecimalType();
        $platform = new PostgreSQLPlatform();

        self::assertNull($type->convertToPHPValue(null, $platform));
    }

    public function testRequiresSqlCommentHint(): void
    {
        $type = new DecimalType();
        $platform = new PostgreSQLPlatform();

        self::assertTrue($type->requiresSQLCommentHint($platform));
    }
}
