# Coding Standards Reference

Reference for coding standards and conventions used in the project.

## Git Commit Messages

All commits **MUST** respect the [Conventional Commits specifications](https://www.conventionalcommits.org/en/v1.0.0/).

### Allowed Commit Types

- **feat** – A new feature is introduced
- **fix** – A bug fix has occurred
- **chore** – Changes that don't modify src or test files (e.g., dependencies)
- **refactor** – Refactored code that neither fixes a bug nor adds a feature
- **docs** – Updates to documentation
- **style** – Code formatting changes (white-space, semi-colons, etc.)
- **test** – Adding or correcting tests
- **perf** – Performance improvements
- **ci** – Continuous integration related
- **build** – Build system or external dependencies
- **revert** – Reverts a previous commit

### Examples

```bash
git commit -m "feat: add article recommendation endpoint"
git commit -m "fix: resolve cache invalidation issue"
git commit -m "docs: update installation instructions"
```

## PHP Coding Standards

The project follows:
- [PER Coding Style](https://www.php-fig.org/per/) (an evolution of PSR-12)
- [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html)

### Enforcement

- **ECS (Easy Coding Standard)**: Automatically checks code style
- Runs in CI pipeline on every push
- Can be run locally: `vendor/bin/ecs check`

### Auto-fix

Many style issues can be automatically fixed:

```bash
vendor/bin/ecs check --fix
```

