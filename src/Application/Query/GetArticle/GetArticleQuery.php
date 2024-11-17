<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticle;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 3600, tags: ['article'])]
final class GetArticleQuery
{
    public function __construct(
        public string $identifier
    ) {}
}
