<?php

declare(strict_types=1);

namespace App\Application\Query\BatchArticle;

final readonly class BatchArticleQuery
{
    public function __construct(
        /** @var string[] $identifiers */
        public array $identifiers
    ) {}
}
