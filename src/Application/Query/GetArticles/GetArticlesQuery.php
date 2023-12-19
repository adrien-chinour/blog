<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticles;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetArticlesQuery implements CacheableQueryInterface
{
    public function __construct(
        public ?int $limit = null,
    ) {}

    public function getCacheKey(): string
    {
        return 'articles';
    }

    public function getCacheTtl(): int
    {
        return 3600;
    }
}
