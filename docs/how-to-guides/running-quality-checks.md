# How to Run Quality Checks

This guide explains how to run code quality checks locally. The CI runs exactly the same composer scripts, so a green `make quality` locally means a green CI.

## Running All Quality Checks

```bash
# Using Makefile (recommended)
make quality

# Or using docker compose directly
docker compose run --rm php composer run-script quality
```

This command runs, in order:

| Script | Tool | Purpose |
|--------|------|---------|
| `composer validate --strict` | Composer | Validates composer.json and composer.lock |
| `composer quality:lint` | Symfony console | `lint:yaml` on config/ and `lint:container` |
| `composer quality:phpstan` | PHPStan (level 9) | Static analysis |
| `composer quality:ecs` | Easy Coding Standard | Coding standards |
| `composer quality:arkitect` | PHPArkitect | Architecture rules (`tests/Architecture/arkitect.php`) |
| `composer quality:rector` | Rector (dry-run) | Fails if automated refactorings are pending (`rector.php`) |
| `composer quality:deps` | composer-require-checker, composer-unused | Dependencies must be declared explicitly and actually used |

## Individual Checks

Every check is available as its own composer script, extra options go after `--`:

```bash
docker compose run --rm php composer quality:phpstan
docker compose run --rm php composer quality:ecs -- --no-progress-bar
docker compose run --rm php composer quality:arkitect
docker compose run --rm php composer quality:rector
docker compose run --rm php composer quality:deps
docker compose run --rm php composer quality:lint
```

## Fixing Issues Automatically

```bash
# Code style
docker compose run --rm php vendor/bin/ecs check --fix

# Apply pending Rector refactorings (review the diff afterwards)
docker compose run --rm php vendor/bin/rector process
```

## Dependency Hygiene

- `composer-require-checker` reports symbols used in `src/` whose package is not in `require`. Fix it by adding the package to composer.json, even when it is already pulled transitively.
- `composer-unused` reports packages in `require` that no PHP code imports. Packages wired only through `config/` (bundles, runtime, Flex...) are whitelisted in `composer-unused.php`.

## Tests and Coverage

```bash
make test            # All test suites
make test-unit       # Unit tests only
make test-integration
make test-coverage   # Clover, Cobertura and HTML reports in var/coverage/ (needs pcov or XDEBUG_MODE=coverage)
make test-mutation   # Infection mutation testing on the Unit suite (infection.json5)
```

The CI fails under 70% line coverage and under 80% MSI (mutation score indicator).
