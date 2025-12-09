# Docker Services Reference

Complete reference for all Docker services defined in `compose.yml`.

## Services Overview

| Service | Container Name | Port | Purpose |
|---------|---------------|------|---------|
| PHP | `blog.php` | 8080 | Application server |
| Meilisearch | `blog.search` | 7700 | Search engine |
| OpenTelemetry Collector | `blog.otlp-collector` | 4317, 4318 | Telemetry collection |
| Zipkin | `blog.zipkin` | 9411 | Distributed tracing UI |

## PHP Service

### Configuration

- **Image**: Built from `frankenphp_dev` target
- **Base**: FrankenPHP with PHP 8.4
- **Port**: 8080
- **Volumes**:
  - `./:/app` - Project code
  - `./var/:/app/var` - Application data
  - Caddy configuration files

### Environment Variables

- `SERVER_NAME`: Server name (default: `:8080`)
- `XDEBUG_MODE`: Xdebug mode (default: `off`)
- `OTEL_*`: OpenTelemetry configuration

### Health Check

```bash
curl http://localhost:2019/metrics
```

## Meilisearch Service

### Configuration

- **Image**: `getmeili/meilisearch:v1.10`
- **Port**: 7700
- **Volume**: `meilisearch_data` for persistence

### Environment Variables

- `MEILI_MASTER_KEY`: Master key for authentication

### Access

- **API**: `http://localhost:7700`
- **Dashboard**: `http://localhost:7700` (if enabled)

## OpenTelemetry Collector

### Configuration

- **Image**: `otel/opentelemetry-collector-contrib:latest`
- **Ports**: 
  - 4317 (gRPC)
  - 4318 (HTTP)
- **Config**: `otel-config.yaml`

### Purpose

Collects telemetry data from the application and forwards to Zipkin.

## Zipkin

### Configuration

- **Image**: `openzipkin/zipkin:latest`
- **Port**: 9411

### Access

- **UI**: `http://localhost:9411`

### Purpose

Provides distributed tracing visualization.

## Volumes

### Persistent Volumes

- `meilisearch_data`: Meilisearch data persistence
- `caddy_data`: Caddy server data
- `caddy_config`: Caddy configuration

### Clearing Volumes

```bash
docker compose down -v  # Removes all volumes
```

## Network

All services are on the default Docker network and can communicate using service names:
- `http://search:7700` (Meilisearch from PHP)
- `http://otel-collector:4318` (OTLP endpoint)

## See Also

- [How to Use Docker](../how-to-guides/using-docker.md)

