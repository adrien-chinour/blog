<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticle;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetArticleQuery implements CacheableQueryInterface
{
    public function __construct(public string $identifier, public bool $preview = false) {}

    public function getCacheKey(): string
    {
        return sprintf('article_%s', $this->identifier);
    }

    public function getCacheTtl(): int
    {
        return $this->preview ? 0 : 3600;
    }
}
