# How to Invalidate Cache

This guide explains how to invalidate cached query results.

## Using the API Endpoint

Cache entries can be invalidated by tag through the `/cache/invalidation` endpoint.

### Basic Usage

```bash
# Invalidate all cache entries tagged with 'article'
curl -X POST "https://{host}/cache/invalidation?tag[]=article"
```

### Invalidating Multiple Tags

```bash
# Invalidate multiple tags at once
curl -X POST "https://{host}/cache/invalidation?tag[]=article&tag[]=project"
```

### With Authentication

For production environments, authentication is required:

```bash
curl -X POST \
  -H "Authorization: Bearer {your-token}" \
  "https://{host}/cache/invalidation?tag[]=article"
```

## How Cache Tags Work

When a query is cached, it can be tagged. For example:

```php
#[QueryCache(
    ttl: 3600,
    tags: ['article'],
)]
```

All queries tagged with `'article'` will be invalidated when you call the endpoint with `tag[]=article`.

## See Also

- [Cache Reference](../reference/cache.md)
- [Security Guide](../reference/security.md)

