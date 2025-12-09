# CI/CD Workflows Reference

Reference for all GitHub Actions workflows.

## Quality Checks (`quality.yaml`)

**Trigger**: Automatically on every push and pull request.

Runs code quality and security validation:

- **Code Quality**: ECS (coding standards), PHPStan (static analysis), PHPUnit (tests with coverage)
- **Code Analysis**: SonarCloud integration for quality metrics
- **Security**: SBOM generation and vulnerability scanning with Anchore

**Required Secret**: `SONAR_TOKEN`

## Load Testing (`load_test.yaml`)

**Trigger**: Manual workflow dispatch.

Runs performance tests using [k6](https://k6.io/):

- **Smoke Tests**: Basic API functionality validation
- **Stress Tests**: Performance bottleneck identification
- Results available in k6 Cloud dashboard

**Required Secrets**: `K6_CLOUD_TOKEN`, `K6_CLOUD_PROJECT_ID`, `ADMIN_AUTH_TOKEN`

## Docker Base Image (`docker_base_image.yaml`)

**Trigger**: Manual workflow dispatch.

Builds and publishes Docker base image to GitHub Container Registry:
- Image: `ghcr.io/adrien-chinour/blog:base-php8.4`
- Base: FrankenPHP with PHP 8.4

## Quality Gates

The CI pipeline enforces:
- ✅ Composer validation
- ✅ Code style (ECS)
- ✅ Static analysis (PHPStan)
- ✅ Test coverage
- ✅ Architecture rules (PHPArkitect)
- ✅ Security scanning
- ✅ SonarCloud quality gates

## Local Quality Checks

Run quality checks locally before pushing:

```bash
# Using Makefile (recommended)
make quality      # All quality checks
make test         # Run tests only
make test-unit
make test-integration

# Or using docker compose directly
docker compose run --rm php composer run-script quality
docker compose run --rm php vendor/bin/phpunit
docker compose run --rm php vendor/bin/phpunit --testsuite Unit
docker compose run --rm php vendor/bin/phpunit --testsuite Integration
```

