<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleList;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetArticleListQuery implements CacheableQueryInterface
{
    public function __construct(
        public ?int $limit = null,
    ) {}

    public function getCacheKey(): string
    {
        return sprintf('articles_%s', $this->limit);
    }

    public function getCacheTtl(): int
    {
        return 3600;
    }
}
