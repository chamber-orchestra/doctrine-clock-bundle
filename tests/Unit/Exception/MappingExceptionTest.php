<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\DoctrineClockBundle\Exception\MappingException;
use ChamberOrchestra\MetadataBundle\Exception\MappingException as BaseMappingException;
use PHPUnit\Framework\TestCase;

final class MappingExceptionTest extends TestCase
{
    public function testItExtendsMetadataMappingException(): void
    {
        $exception = new MappingException('message');

        self::assertInstanceOf(BaseMappingException::class, $exception);
    }
}
