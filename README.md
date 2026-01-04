[![PHP Composer](https://github.com/chamber-orchestra/doctrine-clock-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/doctrine-clock-bundle/actions/workflows/php.yml)

# Doctrine Clock Bundle

A small Symfony bundle that integrates `symfony/clock` with Doctrine ORM. It provides timestamp traits, interfaces, and a Doctrine event subscriber that auto-populates `createdDatetime` and `updatedDatetime` fields using `DatePoint`.

## Installation

```bash
composer require chamber-orchestra/doctrine-clock-bundle
```

If you are not using Symfony Flex, register the bundle manually:

```php
// config/bundles.php
return [
    ChamberOrchestra\DoctrineClockBundle\ChamberOrchestraDoctrineClockBundle::class => ['all' => true],
];
```

## Usage

Add timestamp fields to your Doctrine entities by using the provided traits and interfaces.

```php
use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampInterface;
use ChamberOrchestra\DoctrineClockBundle\Entity\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Article implements TimestampInterface
{
    use TimestampTrait;

    // ... your fields
}
```

The subscriber will set `createdDatetime` on insert and `updatedDatetime` on insert/update. If you prefer precision with microseconds, use `PrecisedTimestampTrait` instead.

## Custom DBAL Types (Optional)

The bundle includes DBAL type overrides for timestamp precision and decimal handling. If you want to use them, register them in Doctrine:

```php
// config/packages/doctrine.php
return [
    'dbal' => [
        'types' => [
            'datetime' => ChamberOrchestra\DoctrineClockBundle\Type\DateTimeType::class,
            'datetime_immutable' => ChamberOrchestra\DoctrineClockBundle\Type\DateTimeImmutableType::class,
            'decimal' => ChamberOrchestra\DoctrineClockBundle\Type\DecimalType::class,
        ],
    ],
];
```

## Dependencies

- PHP 8.4+
- `symfony/clock` 8.0.*
- `chamber-orchestra/metadata-bundle` 8.0.*

See `composer.json` for the full list.

## Tests

```bash
composer test
```

This runs PHPUnit using `phpunit.xml.dist`.
