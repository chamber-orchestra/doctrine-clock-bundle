<?php

declare(strict_types=1);

namespace Tests\Integrational;

use Doctrine\ORM\Tools\SchemaTool;

final class SchemaValidationTest extends DoctrineTestCase
{
    public function testSchemaIsInSync(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->entityManager);

        $sql = $tool->getUpdateSchemaSql($metadata);

        self::assertSame([], $sql);
    }
}
