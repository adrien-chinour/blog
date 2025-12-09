# Cache Reference

Complete reference for query caching functionality.

## QueryCache Attribute

Query results can be cached using the `QueryCache` attribute on query classes that implement `CacheableQueryInterface`.

### Example

```php
#[QueryCache(
    ttl: 3600,
    tags: ['article'],
)]
final readonly class GetArticleQuery implements CacheableQueryInterface
{
    public function __construct(
        public string $slug
    ) {}
}
```

## Configuration Parameters

- **`ttl`**: Cache duration in seconds (Time To Live)
- **`tags`**: Array of tags for cache invalidation

## Implementation

- Cache implementation: `App\Infrastructure\Component\Cache`
- Middleware: `App\Infrastructure\Symfony\Messenger\Middleware\CacheMiddleware`
- The middleware intercepts queries with the `QueryCache` attribute automatically

## Cache Invalidation

Cache entries can be invalidated by tag through the `/cache/invalidation` endpoint.

See [How to Invalidate Cache](../how-to-guides/invalidating-cache.md) for usage instructions.

