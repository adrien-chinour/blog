# Environment Variables Reference

Complete reference of all environment variables used in the project.

## Required Variables

### Contentful Integration

- `CONTENTFUL_SPACE_ID`: Contentful space identifier
- `CONTENTFUL_ACCESS_TOKEN`: Contentful API access token

### GitHub Integration

- `GITHUB_USER`: GitHub username
- `GITHUB_ACCESS_TOKEN`: GitHub personal access token

### Strapi Integration

- `STRAPI_HOST`: Strapi CMS host URL
- `STRAPI_TOKEN`: Strapi API authentication token

### Meilisearch

- `MEILISEARCH_HOST`: Meilisearch server URL (default: `http://search:7700`)
- `MEILISEARCH_TOKEN`: Meilisearch master key

### Security

- `ADMIN_TOKEN`: Admin authentication token for protected endpoints
- `APP_SECRET`: Symfony application secret

### Observability

- `SENTRY_DSN`: Sentry DSN for error tracking (optional)

## Development Variables

For local development, you can use InMemory repositories which don't require external service credentials.

## Docker Compose Variables

The following variables are set in `compose.yml`:

- `MEILI_MASTER_KEY`: Meilisearch master key (default: `!ChangeMe!`)
- `XDEBUG_MODE`: Xdebug mode (default: `off`)
- `OTEL_*`: OpenTelemetry configuration variables

## Configuration File

Environment variables are loaded from:
1. `.env` (template file)
2. `.env.local` (your local configuration - not committed to git)

## See Also

- [Getting Started Tutorial](../tutorials/getting-started.md)

