# Observability Reference

Complete reference for observability features (OpenTelemetry, Sentry, logging).

## OpenTelemetry

### Configuration

OpenTelemetry is configured via environment variables in `compose.yml`:

```env
OTEL_PHP_AUTOLOAD_ENABLED=true
OTEL_SERVICE_NAME=blog
OTEL_TRACES_EXPORTER=otlp
OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf
OTEL_EXPORTER_OTLP_ENDPOINT=http://otel-collector:4318
OTEL_PROPAGATORS=baggage,tracecontext
```

### Collector

The OpenTelemetry Collector:
- Receives traces from the application
- Forwards to Zipkin for visualization
- Configured in `otel-config.yaml`

### Accessing Traces

View traces in Zipkin UI:

```
http://localhost:9411
```

## Sentry

### Configuration

Sentry is configured in `config/packages/sentry.yaml`:

```yaml
sentry:
    dsn: '%env(SENTRY_DSN)%'
```

### Environment Variable

```env
SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id
```

### Features

- Automatic error tracking
- Performance monitoring
- Release tracking

## Logging

### Monolog Configuration

Logging is configured in `config/packages/monolog.yaml`.

### Log Files

Logs are written to:
- `var/log/dev.log` (development)
- `var/log/prod.log` (production)
- `var/log/deprecations.log` (deprecation warnings)

### Log Levels

- **DEBUG**: Detailed debugging information
- **INFO**: Informational messages
- **WARNING**: Warning messages
- **ERROR**: Error messages

### Viewing Logs

```bash
# View application logs
tail -f var/log/dev.log

# View container logs
make logs
```

## Metrics

### Caddy Metrics

Caddy exposes metrics at:

```
http://localhost:2019/metrics
```

### Health Check

The Docker health check uses Caddy metrics:

```bash
curl http://localhost:2019/metrics
```

## See Also

- [Docker Services Reference](./docker-services.md)

