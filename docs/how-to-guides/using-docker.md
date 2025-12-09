# How to Use Docker

This guide explains how to work with Docker containers in this project.

## Starting Services

### Initial Setup

```bash
make install  # Builds, starts containers, and installs dependencies
```

### Starting Existing Containers

```bash
make up      # Start containers in detached mode
make start   # Build and start containers
```

## Container Services

The `compose.yml` defines several services:

### PHP Application (`blog.php`)

- **Port**: 8080
- **Base Image**: FrankenPHP with PHP 8.4
- **Volumes**: Project code mounted at `/app`
- **Environment**: Development mode with hot reload

### Meilisearch (`blog.search`)

- **Port**: 7700
- **Image**: `getmeili/meilisearch:v1.10`
- **Purpose**: Search engine for articles
- **Access**: `http://localhost:7700`

### OpenTelemetry Collector (`blog.otlp-collector`)

- **Ports**: 4317 (gRPC), 4318 (HTTP)
- **Purpose**: Collects telemetry data
- **Config**: `otel-config.yaml`

### Zipkin (`blog.zipkin`)

- **Port**: 9411
- **Purpose**: Distributed tracing UI
- **Access**: `http://localhost:9411`

## Accessing Containers

### PHP Container Shell

```bash
make bash  # Opens interactive shell in PHP container
```

### Running Commands in Container

```bash
make composer c='command'  # Run composer commands
make console c='command'     # Run Symfony console commands
```

## Viewing Logs

```bash
make logs  # View live logs from all containers
```

## Stopping Services

```bash
make down  # Stop all containers
```

## Docker Images

### Development Image

Built from `frankenphp_dev` target:
- Includes Xdebug
- Hot reload enabled
- Development PHP configuration

### Production Image

Built from `frankenphp_prod` target:
- Optimized for production
- No development tools
- Production PHP configuration

### Base Image

The base image (`frankenphp_base`) is published to GitHub Container Registry:
- Image: `ghcr.io/adrien-chinour/blog:base-php8.4`
- Can be rebuilt using the Docker Base Image workflow

## Volume Management

### Persistent Volumes

- `meilisearch_data`: Meilisearch data persistence
- `caddy_data`: Caddy server data
- `caddy_config`: Caddy configuration

### Clearing Volumes

To reset all data:

```bash
docker compose down -v  # Removes containers and volumes
```

## Health Checks

The PHP container includes a health check:

```bash
curl http://localhost:2019/metrics  # Caddy metrics endpoint
```

## Troubleshooting

### Container Won't Start

```bash
make down
make build  # Rebuild images
make up
```

### Permission Issues

```bash
# Fix file permissions
docker compose exec php chown -R www-data:www-data /app/var
```

### View Container Status

```bash
docker compose ps  # List all containers and their status
```

## See Also

- [Docker Services Reference](../reference/docker-services.md)
- [Makefile Commands Reference](../reference/makefile-commands.md)

