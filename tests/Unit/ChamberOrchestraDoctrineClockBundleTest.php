<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\DoctrineClockBundle\ChamberOrchestraDoctrineClockBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ChamberOrchestraDoctrineClockBundleTest extends TestCase
{
    public function testItIsASymfonyBundle(): void
    {
        $bundle = new ChamberOrchestraDoctrineClockBundle();

        self::assertInstanceOf(Bundle::class, $bundle);
    }
}
