# Repository Guidelines

## Project Structure & Module Organization
- `src/` holds the bundle code, organized by responsibility: `DependencyInjection/`, `EventSubscriber/`, `Type/`, `Entity/`, `Contracts/`, and `Exception/`.
- `src/Resources/config/services.php` contains Symfony service configuration for the bundle.
- `tests/` is reserved for PHPUnit tests with `Integrational/` and `Unit/` subfolders; `tests/Integrational/TestKernel.php` provides a kernel for integration testing.
- `composer.json` defines runtime dependencies and autoloading (`ChamberOrchestra\\DoctrineClockBundle\\` → `src/`).

## Build, Test, and Development Commands
- `composer install` installs dependencies for local development.
- `composer test` runs the PHPUnit suite via `vendor/bin/phpunit`.
- `vendor/bin/phpunit` can be run directly for custom flags (uses `phpunit.xml.dist`).

## Coding Style & Naming Conventions
- PHP 8.4+, strict types: files declare `declare(strict_types=1);`.
- Follow PSR-4 namespaces and directory mapping; new classes should live under `src/` with matching namespaces.
- Keep formatting consistent with existing files (PSR-12 style, 4-space indentation, braces on new lines).
- Traits and interfaces follow descriptive names such as `TimestampCreateTrait` and `TimestampInterface`.

## Testing Guidelines
- PHPUnit is the test runner; configuration lives in `phpunit.xml.dist`.
- Place integration tests under `tests/Integrational/` and unit tests under `tests/Unit/`.
- Name tests using the standard `*Test.php` suffix and group by class or feature.
- When adding behavior, include a matching test and run `composer test`.

## Commit & Pull Request Guidelines
- This repository has no commit history yet; use short, imperative commit subjects (e.g., “Add timestamp precision trait”).
- Include scope in the subject when helpful (e.g., `Type:` or `Entity:` prefixes).
- PRs should include: a brief summary, rationale, and test results (`composer test`), plus any relevant configuration notes.

## Configuration & Environment Notes
- Target Symfony 8.0 and PHP 8.4 as defined in `composer.json`.
- Avoid introducing dependencies that conflict with `symfony/symfony` (explicit conflict is declared).
