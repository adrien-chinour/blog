<?php

declare(strict_types=1);

namespace App\Domain\Social;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class Comment
{
    private function __construct(
        public string            $author,
        public DateTimeInterface $publishedAt,
        public string            $content,
        public string            $articleIdentifier
    ) {}

    public static function create(string $author, string $content, string $articleIdentifier): self
    {
        return new self(
            author: $author,
            publishedAt: new DateTimeImmutable(),
            content: $content,
            articleIdentifier: $articleIdentifier,
        );
    }
}
