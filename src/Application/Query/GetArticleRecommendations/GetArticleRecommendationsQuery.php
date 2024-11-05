<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleRecommendations;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 86_400, tags: ['recommendations'])]
final readonly class GetArticleRecommendationsQuery
{
    public function __construct(public string $articleIdentifier) {}
}
