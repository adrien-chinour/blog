<?php

declare(strict_types=1);

namespace App\Application\Command\SearchArticleIndexation;

final readonly class SearchArticleIndexationCommand
{
    public function __construct(
        public string $articleId
    ) {}
}
