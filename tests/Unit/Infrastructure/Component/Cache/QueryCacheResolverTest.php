<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\Cache;

use App\Application\Query\QueryCache;
use App\Infrastructure\Component\Cache\QueryCacheConfig;
use App\Infrastructure\Component\Cache\QueryCacheResolver;
use PHPUnit\Framework\TestCase;

final class QueryCacheResolverTest extends TestCase
{
    private QueryCacheResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new QueryCacheResolver();
    }

    public function testResolveReturnsNullWhenNoCacheAttribute(): void
    {
        $query = new class {
            public string $slug = 'test-slug';
        };

        $result = $this->resolver->resolve($query);

        $this->assertNull($result);
    }

    public function testResolveReturnsConfigWhenCacheAttributePresent(): void
    {
        $query = new #[QueryCache(ttl: 3600, tags: ['article'])] class {
            public string $slug = 'test-slug';
        };

        $result = $this->resolver->resolve($query);

        $this->assertInstanceOf(QueryCacheConfig::class, $result);
        $this->assertSame(3600, $result->ttl);
        $this->assertSame(['article'], $result->tags);
        $this->assertNotEmpty($result->key);
    }

    public function testResolveGeneratesConsistentCacheKey(): void
    {
        $query = new #[QueryCache(ttl: 3600, tags: ['article'])] class {
            public string $slug = 'test-slug';
        };

        $result1 = $this->resolver->resolve($query);
        $result2 = $this->resolver->resolve($query);

        $this->assertSame($result1->key, $result2->key);
    }

    public function testResolveGeneratesDifferentKeysForDifferentQueries(): void
    {
        $query1 = new #[QueryCache(ttl: 3600, tags: ['article'])] class {
            public string $slug = 'slug-1';
        };

        $query2 = new #[QueryCache(ttl: 3600, tags: ['article'])] class {
            public string $slug = 'slug-2';
        };

        $result1 = $this->resolver->resolve($query1);
        $result2 = $this->resolver->resolve($query2);

        $this->assertNotSame($result1->key, $result2->key);
    }

    public function testResolveHandlesMultipleProperties(): void
    {
        $query = new #[QueryCache(ttl: 1800, tags: ['article', 'featured'])] class {
            public string $slug = 'test-slug';
            public int $limit = 10;
            public bool $published = true;
        };

        $result = $this->resolver->resolve($query);

        $this->assertInstanceOf(QueryCacheConfig::class, $result);
        $this->assertSame(1800, $result->ttl);
        $this->assertSame(['article', 'featured'], $result->tags);
    }

    public function testResolveHandlesEmptyTags(): void
    {
        $query = new #[QueryCache(ttl: 3600, tags: [])] class {
            public string $slug = 'test-slug';
        };

        $result = $this->resolver->resolve($query);

        $this->assertInstanceOf(QueryCacheConfig::class, $result);
        $this->assertSame([], $result->tags);
    }
}

