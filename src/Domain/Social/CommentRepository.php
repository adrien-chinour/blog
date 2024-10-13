<?php

declare(strict_types=1);

namespace App\Domain\Social;

use App\Domain\Social\Exception\FailToPublishCommentException;
use DateTimeImmutable;

interface CommentRepository
{
    /**
     * @return Comment[]
     */
    public function getArticleComments(string $articleIdentifier): array;

    /**
     * @throws FailToPublishCommentException
     */
    public function createComment(string $articleIdentifier, string $username, string $message, DateTimeImmutable $publishedAt): void;
}
