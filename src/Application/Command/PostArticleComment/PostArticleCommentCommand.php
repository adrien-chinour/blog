<?php

declare(strict_types=1);

namespace App\Application\Command\PostArticleComment;

use DateTimeImmutable;

final readonly class PostArticleCommentCommand
{
    public function __construct(
        public string $articleIdentifier,
        public string $username,
        public string $message,
        public DateTimeImmutable $publishedAt = new DateTimeImmutable(),
    ) {}
}
