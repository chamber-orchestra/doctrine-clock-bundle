[![PHP Composer](https://github.com/chamber-orchestra/doctrine-clock-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/doctrine-clock-bundle/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/chamber-orchestra/doctrine-clock-bundle/graph/badge.svg)](https://codecov.io/gh/chamber-orchestra/doctrine-clock-bundle)
[![PHPStan](https://img.shields.io/badge/PHPStan-max-brightgreen)](https://phpstan.org/)
[![Latest Stable Version](https://poser.pugx.org/chamber-orchestra/doctrine-clock-bundle/v)](https://packagist.org/packages/chamber-orchestra/doctrine-clock-bundle)
[![License](https://poser.pugx.org/chamber-orchestra/doctrine-clock-bundle/license)](https://packagist.org/packages/chamber-orchestra/doctrine-clock-bundle)
![PHP 8.5](https://img.shields.io/badge/PHP-8.5-blue?logo=php)
![Doctrine ORM 3](https://img.shields.io/badge/Doctrine%20ORM-3-orange?logo=doctrine)
![Symfony 8](https://img.shields.io/badge/Symfony-8-purple?logo=symfony)

# Doctrine Clock Bundle

A Symfony bundle that automatically manages `createdDatetime` and `updatedDatetime` fields on Doctrine ORM entities using PHP attributes and `symfony/clock` `DatePoint`.

Drop `#[CreateTimestamp]` / `#[UpdateTimestamp]` on any entity property (or use the provided traits) and the bundle handles the rest -- no interfaces, no manual event wiring.

## Requirements

- PHP 8.5+
- Symfony 8.0
- Doctrine ORM 3.6+

## Installation

```bash
composer require chamber-orchestra/doctrine-clock-bundle
```

If you are not using Symfony Flex, register the bundles manually:

```php
// config/bundles.php
return [
    ChamberOrchestra\MetadataBundle\ChamberOrchestraMetadataBundle::class => ['all' => true],
    ChamberOrchestra\DoctrineClockBundle\ChamberOrchestraDoctrineClockBundle::class => ['all' => true],
];
```

## Quick Start

Use the provided traits to add timestamp fields to your entities:

```php
use ChamberOrchestra\DoctrineClockBundle\Entity\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Article
{
    use TimestampTrait; // adds createdDatetime + updatedDatetime

    // ... your fields
}
```

That's it. On `persist`, both fields are set to the current `DatePoint`. On `update`, only `updatedDatetime` is refreshed. Manually pre-populated values are preserved on insert.

## Available Traits

| Trait | Fields | Column precision |
|---|---|---|
| `TimestampTrait` | `createdDatetime` + `updatedDatetime` | seconds |
| `TimestampCreateTrait` | `createdDatetime` only | seconds |
| `TimestampUpdateTrait` | `updatedDatetime` only | seconds |
| `PrecisedTimestampTrait` | `createdDatetime` + `updatedDatetime` | microseconds (scale: 6) |
| `PrecisedTimestampCreateTrait` | `createdDatetime` only | microseconds (scale: 6) |
| `PrecisedTimestampUpdateTrait` | `updatedDatetime` only | microseconds (scale: 6) |

## Using Attributes Directly

If you prefer full control over your entity properties, use the attributes without traits:

```php
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\CreateTimestamp;
use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\UpdateTimestamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\DatePoint;

#[ORM\Entity]
class Article
{
    #[CreateTimestamp]
    #[ORM\Column(type: 'date_point', nullable: false)]
    private DatePoint $createdAt;

    #[UpdateTimestamp]
    #[ORM\Column(type: 'date_point', nullable: false)]
    private DatePoint $modifiedAt;

    // ... getters, setters
}
```

Multiple fields with the same attribute are supported -- e.g. two `#[CreateTimestamp]` properties will both be set on insert.

## Behaviour

| Event | `#[CreateTimestamp]` | `#[UpdateTimestamp]` |
|---|---|---|
| `prePersist` | Set if `null` | Set if `null` |
| `preUpdate` | Not touched | Always overwritten |

## Custom DBAL Types (Optional)

The bundle ships DBAL type overrides for improved timestamp precision and decimal handling:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            datetime: ChamberOrchestra\DoctrineClockBundle\Type\DateTimeType
            datetime_immutable: ChamberOrchestra\DoctrineClockBundle\Type\DateTimeImmutableType
            decimal: ChamberOrchestra\DoctrineClockBundle\Type\DecimalType
```

## Development

```bash
composer test        # PHPUnit
composer analyse     # PHPStan (level max)
composer cs-check    # PHP CS Fixer (dry-run)
composer cs-fix      # PHP CS Fixer (apply)
```

## License

MIT -- see [LICENSE](LICENSE).
