<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use ChamberOrchestra\DoctrineClockBundle\Exception\MappingException;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\CreateTimestamp;
use ChamberOrchestra\MetadataBundle\Exception\MappingException as BaseMappingException;
use PHPUnit\Framework\TestCase;

final class MappingExceptionTest extends TestCase
{
    public function testItExtendsMetadataMappingException(): void
    {
        $exception = new MappingException('message');

        self::assertInstanceOf(BaseMappingException::class, $exception);
    }

    public function testUnmappedTimestampFieldMessage(): void
    {
        $exception = MappingException::unmappedTimestampField(
            'App\\Entity\\Foo',
            'notMapped',
            CreateTimestamp::class,
        );

        self::assertInstanceOf(MappingException::class, $exception);
        self::assertSame(
            'Property "notMapped" in "App\\Entity\\Foo" has #[CreateTimestamp] but is not a Doctrine-mapped field.',
            $exception->getMessage(),
        );
    }
}
