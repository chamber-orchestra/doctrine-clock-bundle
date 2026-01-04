<?php

declare(strict_types=1);

namespace Tests\Unit\Contracts;

use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampCreateInterface;
use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampInterface;
use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampUpdateInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TimestampInterfaceTest extends TestCase
{
    public function testTimestampInterfaceExtendsCreateAndUpdate(): void
    {
        $reflection = new ReflectionClass(TimestampInterface::class);

        self::assertTrue($reflection->implementsInterface(TimestampCreateInterface::class));
        self::assertTrue($reflection->implementsInterface(TimestampUpdateInterface::class));
    }
}
