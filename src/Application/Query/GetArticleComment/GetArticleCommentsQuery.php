<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleComment;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetArticleCommentsQuery implements CacheableQueryInterface
{
    public function __construct(
        public string $articleId
    ) {}

    public function getCacheKey(): string
    {
        return sprintf('comments_%s', $this->articleId);
    }

    public function getCacheTtl(): int
    {
        return 10;
    }
}
