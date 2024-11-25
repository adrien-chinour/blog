<?php

declare(strict_types=1);

namespace App\Application\Query\GetPage;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 600, tags: ['page'])]
final readonly class GetPageQuery
{
    public function __construct(
        public string $path,
    ) {}
}
