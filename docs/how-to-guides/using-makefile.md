# How to Use Makefile Commands

This guide explains the available Makefile commands for common development tasks.

## Viewing Available Commands

To see all available commands with descriptions:

```bash
make help
```

## Docker Commands

### Build and Start

```bash
make install    # Build, start containers, and install dependencies
make start     # Build and start containers
make build     # Build Docker images
make up        # Start containers in detached mode
make down      # Stop containers
```

### Container Access

```bash
make bash      # Connect to PHP container shell
make logs      # Show live container logs
```

## Composer Commands

```bash
make composer c='require package/name'  # Run composer commands
make vendor                              # Install vendors (production mode)
```

## Testing Commands

```bash
make test              # Run all tests
make test-unit         # Run unit tests only
make test-integration  # Run integration tests only
```

## Quality Checks

```bash
make quality  # Run all quality checks (ECS, PHPStan, PHPArkitect)
```

## Symfony Console

```bash
make console c='about'           # Run Symfony console commands
make cc                           # Clear cache
```

## Documentation

```bash
make doc  # Start Docsify documentation server (requires npm)
```

