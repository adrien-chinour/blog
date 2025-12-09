# Makefile Commands Reference

Complete reference of all available Makefile commands.

> **Note**: All commands run inside Docker containers. The Makefile uses `docker compose exec` for running containers and `docker compose run --rm` for one-off commands.

## Docker Commands

| Command | Description | Docker Equivalent |
|---------|-------------|------------------|
| `make install` | Build, start containers, and install dependencies | `docker compose up -d && docker compose exec php composer install` |
| `make start` | Build and start containers | `docker compose build && docker compose up -d` |
| `make build` | Build Docker images | `docker compose build --pull --no-cache` |
| `make up` | Start containers in detached mode | `docker compose up --detach` |
| `make down` | Stop containers | `docker compose down --remove-orphans` |
| `make logs` | Show live container logs | `docker compose logs --tail=0 --follow` |
| `make bash` | Connect to PHP container shell | `docker compose exec php bash` |

## Composer Commands

| Command | Description | Docker Equivalent |
|---------|-------------|-------------------|
| `make composer c='command'` | Run any composer command | `docker compose exec php composer command` |
| `make vendor` | Install vendors (production mode) | `docker compose exec php composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction` |

**Example:**
```bash
make composer c='require symfony/package'
# or
docker compose run --rm php composer require symfony/package
```

## Testing Commands

| Command | Description | Docker Equivalent |
|---------|-------------|-------------------|
| `make test` | Run all tests | `docker compose run --rm php vendor/bin/phpunit` |
| `make test-unit` | Run unit tests only | `docker compose run --rm php vendor/bin/phpunit --testsuite Unit` |
| `make test-integration` | Run integration tests only | `docker compose run --rm php vendor/bin/phpunit --testsuite Integration` |

## Quality Commands

| Command | Description | Docker Equivalent |
|---------|-------------|-------------------|
| `make quality` | Run all quality checks (ECS, PHPStan, PHPArkitect) | `docker compose run --rm php composer run-script quality` |

## Symfony Console Commands

| Command | Description | Docker Equivalent |
|---------|-------------|-------------------|
| `make console c='command'` | Run any Symfony console command | `docker compose run --rm php bin/console command` |
| `make cc` | Clear cache | `docker compose run --rm php bin/console cache:clear` |

**Example:**
```bash
make console c='about'
# or
docker compose run --rm php bin/console about
```

## Documentation Commands

| Command | Description |
|---------|-------------|
| `make doc` | Start Docsify documentation server (requires npm) |

## Help

To see all available commands:

```bash
make help
```

