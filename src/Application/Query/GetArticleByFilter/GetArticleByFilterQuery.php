<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleByFilter;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 3600, tags: ['get_article', 'article'])]
final readonly class GetArticleByFilterQuery
{
    public function __construct(
        public array $filters,
    ) {}
}
