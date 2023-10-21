<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleByFilter;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetArticleByFilterQuery implements CacheableQueryInterface
{
    public function __construct(public array $filters) {}

    public function getCacheKey(): string
    {
        return sprintf('article_filters_%s', md5(json_encode($this->filters)));
    }

    public function getCacheTtl(): int
    {
        return 3600;
    }
}
