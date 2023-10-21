<?php

declare(strict_types=1);

namespace App\Application\Query\GetLatestArticles;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetLatestArticlesQuery implements CacheableQueryInterface
{
    public function __construct(public int $limit = 3) {}

    public function getCacheKey(): string
    {
        return sprintf('latest_articles_%s', $this->limit);
    }

    public function getCacheTtl(): int
    {
        return 3600;
    }
}
