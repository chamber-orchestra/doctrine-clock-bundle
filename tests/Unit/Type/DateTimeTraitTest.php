<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;

final class DateTimeTraitTest extends TestCase
{
    public function testGetSQLDeclarationUsesScaleWhenProvided(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new PostgreSQLPlatform();

        self::assertSame(
            'TIMESTAMP(6) WITHOUT TIME ZONE',
            $type->getSQLDeclaration(['scale' => 6], $platform)
        );
    }

    public function testGetSQLDeclarationDefaultsToZeroScale(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new PostgreSQLPlatform();

        self::assertSame(
            'TIMESTAMP(0) WITHOUT TIME ZONE',
            $type->getSQLDeclaration([], $platform)
        );
    }

    public function testGetSQLDeclarationRejectsNegativeScale(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new PostgreSQLPlatform();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('between 0 and 6');
        $type->getSQLDeclaration(['scale' => -1], $platform);
    }

    public function testGetSQLDeclarationRejectsScaleAboveSix(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new PostgreSQLPlatform();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('between 0 and 6');
        $type->getSQLDeclaration(['scale' => 7], $platform);
    }

    public function testGetSQLDeclarationRejectsNonIntegerScale(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new PostgreSQLPlatform();

        $this->expectException(\InvalidArgumentException::class);
        $type->getSQLDeclaration(['scale' => 'six'], $platform);
    }

    public function testGetSQLDeclarationDelegatesToPlatformForNonPostgreSQL(): void
    {
        $type = new class {
            use \ChamberOrchestra\DoctrineClockBundle\Type\DateTimeTrait;
        };

        $platform = new MySQLPlatform();

        $declaration = $type->getSQLDeclaration(['scale' => 6], $platform);

        self::assertStringNotContainsString('WITHOUT TIME ZONE', $declaration);
    }
}
