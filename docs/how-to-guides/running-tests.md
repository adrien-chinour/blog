# How to Run Tests

This guide explains how to run tests in the project.

## Running All Tests

To run the complete test suite using Docker:

```bash
# Using Makefile (recommended)
make test

# Or using docker compose directly
docker compose run --rm php vendor/bin/phpunit
```

## Running Specific Test Suites

### Unit Tests Only

```bash
# Using Makefile (recommended)
make test-unit

# Or using docker compose directly
docker compose run --rm php vendor/bin/phpunit --testsuite Unit
```

### Integration Tests Only

```bash
# Using Makefile (recommended)
make test-integration

# Or using docker compose directly
docker compose run --rm php vendor/bin/phpunit --testsuite Integration
```

### Running Specific Test Files

```bash
# Run a specific test file
docker compose run --rm php vendor/bin/phpunit tests/Unit/Domain/Social/CommentValidatorTest.php

# Run tests in a specific directory
docker compose run --rm php vendor/bin/phpunit tests/Unit/Domain
```

## Test Structure

The project organizes tests into:

- **Unit Tests** (`tests/Unit/`): Test individual components in isolation
- **Integration Tests** (`tests/Integration/`): Test component interactions and HTTP endpoints
- **Architecture Tests** (`tests/Architecture/`): Enforce architectural rules using PHPArkitect

## Debugging Tests

To debug tests or run PHP commands in the container:

```bash
# Open a shell in the container
make bash
# or
docker compose run --rm php bash

# Then run commands directly inside the container
vendor/bin/phpunit tests/Unit/Domain
```

Or run commands directly with docker compose:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Unit/Domain
```

