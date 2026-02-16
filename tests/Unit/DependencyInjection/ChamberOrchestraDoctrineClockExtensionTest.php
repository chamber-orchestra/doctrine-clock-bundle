<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use ChamberOrchestra\DoctrineClockBundle\DependencyInjection\ChamberOrchestraDoctrineClockExtension;
use ChamberOrchestra\DoctrineClockBundle\EventSubscriber\TimestampSubscriber;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Driver\TimestampDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ChamberOrchestraDoctrineClockExtensionTest extends TestCase
{
    public function testLoadsServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new ChamberOrchestraDoctrineClockExtension();

        $extension->load([], $container);

        self::assertTrue($container->hasDefinition(TimestampSubscriber::class));
        self::assertTrue($container->hasDefinition(TimestampDriver::class));
        self::assertTrue($container->getDefinition(TimestampSubscriber::class)->isAutoconfigured());
        self::assertTrue($container->getDefinition(TimestampDriver::class)->isAutoconfigured());
    }
}
