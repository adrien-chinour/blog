<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\Cache;

use App\Infrastructure\Component\Cache\QueryCacheConfig;
use PHPUnit\Framework\TestCase;

final class QueryCacheConfigTest extends TestCase
{
    public function testQueryCacheConfigCreation(): void
    {
        $config = new QueryCacheConfig(
            key: 'cache-key-123',
            ttl: 3600,
            tags: ['article', 'featured']
        );

        $this->assertSame('cache-key-123', $config->key);
        $this->assertSame(3600, $config->ttl);
        $this->assertSame(['article', 'featured'], $config->tags);
    }

    public function testQueryCacheConfigWithEmptyTags(): void
    {
        $config = new QueryCacheConfig(
            key: 'cache-key',
            ttl: 1800,
            tags: []
        );

        $this->assertSame([], $config->tags);
    }

    public function testQueryCacheConfigPropertiesArePublic(): void
    {
        $config = new QueryCacheConfig('key', 3600, ['tag']);

        $this->assertIsString($config->key);
        $this->assertIsInt($config->ttl);
        $this->assertIsArray($config->tags);
    }
}

