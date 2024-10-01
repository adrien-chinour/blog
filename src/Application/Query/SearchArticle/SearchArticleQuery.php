<?php

declare(strict_types=1);

namespace App\Application\Query\SearchArticle;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 3600, tags: ['articles'])]
final readonly class SearchArticleQuery
{
    public function __construct(
        public string $term,
    ) {}
}
