# CI/CD Workflows Reference

Reference for all GitHub Actions workflows.

## Quality Checks (`quality.yaml`)

**Trigger**: Every push on `main` and every pull request. Concurrent runs on the same branch are cancelled.

Two jobs run in parallel.

**Quality & tests** (PHP version from `.php-version`, extensions incl. `opentelemetry`, coverage via `pcov`):

- `composer validate --strict`
- ECS (coding standards) — `composer quality:ecs`
- PHPStan (static analysis) — `composer quality:phpstan`
- PHPArkitect (architecture rules) — `composer quality:arkitect`
- PHPUnit with coverage — `composer test-coverage`
- Coverage summary posted to the job summary and as a sticky PR comment; the job fails under 70% line coverage (orange under 85%)
- HTML coverage report uploaded as the `coverage-html` artifact (14 days)

**Security**:

- `composer audit --locked` (PHP security advisories)
- SBOM generation (Anchore Syft) and vulnerability scan (Grype), failing on critical findings
- Scan results uploaded as SARIF to the repository Security tab

The CI calls the same composer scripts as local development, so both stay in sync.

## Load Testing (`load_test.yaml`)

**Trigger**: Manual workflow dispatch.

Runs performance tests using [k6](https://k6.io/):

- **Smoke Tests**: Basic API functionality validation
- **Stress Tests**: Performance bottleneck identification
- Results available in k6 Cloud dashboard

**Required Secrets**: `K6_CLOUD_TOKEN`, `K6_CLOUD_PROJECT_ID`, `ADMIN_AUTH_TOKEN`

## Docker Base Image (`docker_base_image.yaml`)

**Trigger**: Manual workflow dispatch, weekly schedule (Monday 04:00 UTC), and pushes on `main` touching `Dockerfile` or `.docker/`.

Builds and publishes the Docker base image to GitHub Container Registry:
- Image: `ghcr.io/adrien-chinour/blog:base-php8.4`
- Base: FrankenPHP with PHP 8.4, always pulled fresh so upstream PHP security patches land weekly
- The PHP version of the published image is written to the job summary

After a rebuild, redeploy the production image so it picks up the new base.

## Quality Gates

The CI pipeline enforces:
- ✅ Composer validation
- ✅ Code style (ECS)
- ✅ Static analysis (PHPStan)
- ✅ Test coverage (minimum 70% lines)
- ✅ Architecture rules (PHPArkitect)
- ✅ Dependency security (composer audit + Grype)

## Local Quality Checks

Run quality checks locally before pushing:

```bash
# Using Makefile (recommended)
make quality        # All quality checks
make test           # Run tests only
make test-coverage  # Tests with coverage reports in var/coverage/
make test-unit
make test-integration

# Or using docker compose directly
docker compose run --rm php composer run-script quality
docker compose run --rm php vendor/bin/phpunit
docker compose run --rm php vendor/bin/phpunit --testsuite Unit
docker compose run --rm php vendor/bin/phpunit --testsuite Integration
```

