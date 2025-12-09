# Development Workflow

This tutorial guides you through the typical development workflow for contributing to the project.

## Prerequisites

- Completed [Getting Started](getting-started.md) tutorial
- Basic understanding of Git
- Docker and Make installed

## Step 1: Create a Feature Branch

Always create a feature branch from `main`:

```bash
git checkout main
git pull origin main
git checkout -b feat/your-feature-name
```

## Step 2: Make Your Changes

Make your code changes following the [Coding Standards](../reference/coding-standards.md).

## Step 3: Run Quality Checks Locally

Before committing, run quality checks:

```bash
make quality
# or
docker compose run --rm php composer run-script quality
```

This ensures:
- Code style is correct (ECS)
- No static analysis errors (PHPStan)
- Architecture rules are respected (PHPArkitect)

## Step 4: Run Tests

Ensure all tests pass:

```bash
make test
# or
docker compose run --rm php vendor/bin/phpunit
```

Or run specific test suites:

```bash
make test-unit
make test-integration
# or
docker compose run --rm php vendor/bin/phpunit --testsuite Unit
docker compose run --rm php vendor/bin/phpunit --testsuite Integration
```

## Step 5: Commit Your Changes

Commit using [Conventional Commits](../reference/coding-standards.md#git-commit-messages):

```bash
git add .
git commit -m "feat: add new feature description"
```

## Step 6: Push and Create Pull Request

```bash
git push origin feat/your-feature-name
```

Then create a pull request on GitHub. The CI pipeline will automatically:
- Run quality checks
- Run tests
- Perform security scanning
- Analyze code with SonarCloud

## Step 7: Address Review Feedback

Make any requested changes and push updates to your branch. The PR will automatically update.

## Common Development Tasks

### Accessing the Container

```bash
make bash  # Opens shell in PHP container
```

### Running Symfony Console Commands

```bash
make console c='about'  # Run any Symfony command
make cc                 # Clear cache
# or
docker compose run --rm php bin/console about
docker compose run --rm php bin/console cache:clear
```

### Viewing Logs

```bash
make logs  # View live container logs
# or
docker compose logs --tail=0 --follow
```

### Installing New Dependencies

```bash
make composer c='require vendor/package'
# or
docker compose run --rm php composer require vendor/package
```

## See Also

- [Running Tests](../how-to-guides/running-tests.md)
- [Running Quality Checks](../how-to-guides/running-quality-checks.md)
- [Coding Standards](../reference/coding-standards.md)

