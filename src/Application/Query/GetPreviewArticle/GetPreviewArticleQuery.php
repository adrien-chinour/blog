<?php

declare(strict_types=1);

namespace App\Application\Query\GetPreviewArticle;

final readonly class GetPreviewArticleQuery
{
    public function __construct(
        public string $identifier,
    ) {}
}
