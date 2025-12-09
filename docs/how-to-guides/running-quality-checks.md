# How to Run Quality Checks

This guide explains how to run code quality checks locally.

## Running All Quality Checks

Run all quality checks (validation, static analysis, coding standards, architecture):

```bash
composer quality
```

This command runs:
- `composer validate --strict`: Validates composer.json
- `phpstan analyse`: Static analysis with PHPStan
- `ecs check`: Coding standards with Easy Coding Standard
- `phparkitect check`: Architecture rules validation

## Using Docker

If you're using Docker:

```bash
make quality
```

## Individual Checks

### Code Style (ECS)

```bash
vendor/bin/ecs check
```

### Static Analysis (PHPStan)

```bash
vendor/bin/phpstan analyse --memory-limit=-1
```

### Architecture Rules (PHPArkitect)

```bash
vendor/bin/phparkitect check --config=tests/Architecture/arkitect.php
```

## Fixing Code Style Issues

ECS can automatically fix many code style issues:

```bash
vendor/bin/ecs check --fix
```

