# How to Run Quality Checks

This guide explains how to run code quality checks locally.

## Running All Quality Checks

Run all quality checks (validation, static analysis, coding standards, architecture):

```bash
# Using Makefile (recommended)
make quality

# Or using docker compose directly
docker compose run --rm php composer run-script quality
```

This command runs:
- `composer validate --strict`: Validates composer.json
- `phpstan analyse`: Static analysis with PHPStan
- `ecs check`: Coding standards with Easy Coding Standard
- `phparkitect check`: Architecture rules validation

## Individual Checks

### Code Style (ECS)

```bash
docker compose run --rm php vendor/bin/ecs check
```

### Static Analysis (PHPStan)

```bash
docker compose run --rm php vendor/bin/phpstan analyse --memory-limit=-1
```

### Architecture Rules (PHPArkitect)

```bash
docker compose run --rm php vendor/bin/phparkitect check --config=tests/Architecture/arkitect.php
```

## Fixing Code Style Issues

ECS can automatically fix many code style issues:

```bash
docker compose run --rm php vendor/bin/ecs check --fix
```

