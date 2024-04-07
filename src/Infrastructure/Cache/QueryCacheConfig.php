<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

final readonly class QueryCacheConfig
{
    public function __construct(
        public string $key,
        public int $ttl,
        public array $tags,
    ) {}
}
