<?php

declare(strict_types=1);

namespace App\Application\Event\Article;

final readonly class ArticlePublishedEvent
{
    public function __construct(
        public string $articleIdentifier,
    ) {}
}
