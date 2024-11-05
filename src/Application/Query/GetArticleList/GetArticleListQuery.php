<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleList;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 3600, tags: ['article'])]
final readonly class GetArticleListQuery
{
    public function __construct(
        public ?int $limit = null,
    ) {}
}
