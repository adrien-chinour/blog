<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Social\Comment;
use App\Domain\Social\CommentRepository;
use DateTimeImmutable;

final class InMemoryCommentRepository implements CommentRepository
{
    public function __construct(
        /**
         * HashMap with article id and all comments associated.
         * @var array<array<Comment>>
         */
        private array $comments = [],
    ) {}

    public function getArticleComments(string $articleIdentifier): array
    {
        return $this->comments[$articleIdentifier] ?? [];
    }

    public function createComment(string $articleIdentifier, string $username, string $message, DateTimeImmutable $publishedAt): void
    {
        if (!isset($this->comments[$articleIdentifier])) {
            $this->comments[$articleIdentifier] = [];
        }

        $this->comments[$articleIdentifier][] = new Comment(uniqid(), $username, $message, $publishedAt);
    }
}
