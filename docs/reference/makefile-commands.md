# Makefile Commands Reference

Complete reference of all available Makefile commands.

## Docker Commands

| Command | Description |
|---------|-------------|
| `make install` | Build, start containers, and install dependencies |
| `make start` | Build and start containers |
| `make build` | Build Docker images |
| `make up` | Start containers in detached mode |
| `make down` | Stop containers |
| `make logs` | Show live container logs |
| `make bash` | Connect to PHP container shell |

## Composer Commands

| Command | Description |
|---------|-------------|
| `make composer c='command'` | Run any composer command |
| `make vendor` | Install vendors (production mode) |

**Example:**
```bash
make composer c='require symfony/package'
```

## Testing Commands

| Command | Description |
|---------|-------------|
| `make test` | Run all tests |
| `make test-unit` | Run unit tests only |
| `make test-integration` | Run integration tests only |

## Quality Commands

| Command | Description |
|---------|-------------|
| `make quality` | Run all quality checks (ECS, PHPStan, PHPArkitect) |

## Symfony Console Commands

| Command | Description |
|---------|-------------|
| `make console c='command'` | Run any Symfony console command |
| `make cc` | Clear cache |

**Example:**
```bash
make console c='about'
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

