<?php

declare(strict_types=1);

namespace App\Application\Command\SearchArticleInitIndexation;

final readonly class SearchArticleInitIndexationCommand
{
    public function __construct(
        public bool $rewrite = false,
    ) {}
}
