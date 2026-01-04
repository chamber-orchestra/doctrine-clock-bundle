<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

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
}
