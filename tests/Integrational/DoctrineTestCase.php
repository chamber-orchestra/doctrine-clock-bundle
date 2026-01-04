<?php

declare(strict_types=1);

namespace Tests\Integrational;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;

abstract class DoctrineTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected MockClock $clock;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->clock = new MockClock('2024-01-01 00:00:00', 'UTC');
        Clock::set($this->clock);
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->resetSchema();
    }

    protected function tearDown(): void
    {
        if (isset($this->entityManager) && $this->entityManager->isOpen()) {
            $this->entityManager->clear();
            $this->entityManager->close();
        }
        Clock::set(new NativeClock());

        parent::tearDown();
    }

    protected function resetSchema(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        if (!$metadata) {
            return;
        }

        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    protected function getClock(): MockClock
    {
        return $this->clock;
    }
}
