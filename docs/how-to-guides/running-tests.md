# How to Run Tests

This guide explains how to run tests in the project.

## Running All Tests

To run the complete test suite:

```bash
composer test
# or
bin/phpunit
```

## Running Specific Test Suites

### Unit Tests Only

```bash
composer test-unit
# or
bin/phpunit --testsuite Unit
```

### Integration Tests Only

```bash
composer test-integration
# or
bin/phpunit --testsuite Integration
```

## Test Structure

The project organizes tests into:

- **Unit Tests** (`tests/Unit/`): Test individual components in isolation
- **Integration Tests** (`tests/Integration/`): Test component interactions and HTTP endpoints
- **Architecture Tests** (`tests/Architecture/`): Enforce architectural rules using PHPArkitect

## Using Docker

If you're using Docker, run tests inside the container:

```bash
make test
```

