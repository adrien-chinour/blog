# CI/CD Workflows Reference

Reference for all GitHub Actions workflows.

## Quality Checks (`quality.yaml`)

**Trigger**: Every push on `main` and every pull request. Concurrent runs on the same branch are cancelled.

Three jobs run in parallel. All of them use the PHP version from `.php-version`, with the `opentelemetry` extension, and call the same composer scripts as local development so both stay in sync.

**Quality & tests**:

- `composer validate --strict`
- Symfony lints (`lint:yaml`, `lint:container`) — `composer quality:lint`
- ECS (coding standards) — `composer quality:ecs`
- PHPStan (static analysis) — `composer quality:phpstan`
- PHPArkitect (architecture rules) — `composer quality:arkitect`
- Rector dry-run (no pending automated refactoring) — `composer quality:rector`
- composer-require-checker and composer-unused (dependency hygiene) — `composer quality:deps`
- OpenAPI specification drift check: `composer docs:api` regenerates `docs/reference/openapi.json` and the job fails if it differs from the committed file
- PHPUnit with coverage (pcov) — `composer test-coverage`
- Coverage summary posted to the job summary and as a sticky PR comment; the job fails under 70% line coverage (orange under 85%)
- HTML coverage report uploaded as the `coverage-html` artifact (14 days)

**Mutation testing**:

- Infection on the Unit suite — `composer test-mutation`, fails under 80% MSI
- Logs uploaded as the `infection-logs` artifact

**Security**:

- `composer audit --locked` (PHP security advisories)
- SBOM generation (Anchore Syft) and vulnerability scan (Grype), failing on critical findings
- Scan results uploaded as SARIF to the repository Security tab

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
- The published image is scanned with Grype (fails on critical vulnerabilities with an available fix) and the results are uploaded to the Security tab

After a rebuild, redeploy the production image so it picks up the new base.

## Dependabot (`dependabot.yml`)

Weekly pull requests (Monday morning, Europe/Paris) for Composer packages (grouped: Symfony, OpenTelemetry, dev tools), GitHub Actions (grouped, SHA-pinned), Docker base images and compose images.

## Supply chain

Every GitHub Action is pinned to a commit SHA with the version in a trailing comment. Dependabot keeps the pins up to date.

## Quality Gates

The CI pipeline enforces:
- ✅ Composer validation
- ✅ Code style (ECS)
- ✅ Static analysis (PHPStan)
- ✅ Test coverage (minimum 70% lines)
- ✅ Mutation score (minimum 80% MSI)
- ✅ Architecture rules (PHPArkitect)
- ✅ No pending Rector refactoring
- ✅ OpenAPI specification up to date
- ✅ Dependency hygiene (composer-require-checker, composer-unused)
- ✅ Dependency security (composer audit + Grype)

## Local Quality Checks

Run quality checks locally before pushing:

```bash
# Using Makefile (recommended)
make quality        # All quality checks
make test           # Run tests only
make test-coverage  # Tests with coverage reports in var/coverage/
make test-mutation  # Infection mutation testing
make test-unit
make test-integration

# Or using docker compose directly
docker compose run --rm php composer run-script quality
docker compose run --rm php vendor/bin/phpunit
docker compose run --rm php vendor/bin/phpunit --testsuite Unit
docker compose run --rm php vendor/bin/phpunit --testsuite Integration
```

