<?php

declare(strict_types=1);

namespace App\Application\Query;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class QueryCache
{
    public function __construct(
        public int $ttl = 3600,
        public array $tags = [],
    ) {}
}
