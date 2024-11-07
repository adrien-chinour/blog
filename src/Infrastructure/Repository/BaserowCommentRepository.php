<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Social\CommentRepository as CommentRepositoryInterface;
use App\Domain\Social\Exception\FailToPublishCommentException;
use App\Infrastructure\Baserow\Http\BaserowApiClient;
use DateTimeImmutable;

final readonly class BaserowCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private BaserowApiClient $apiClient,
    ) {}

    public function getArticleComments(string $articleIdentifier): array
    {
        return $this->apiClient->getComments($articleIdentifier);
    }

    /**
     * @throws FailToPublishCommentException
     */
    public function createComment(string $articleIdentifier, string $username, string $message, DateTimeImmutable $publishedAt): void
    {
        try {
            $this->apiClient->postComment($articleIdentifier, $username, $message, $publishedAt);
        } catch (\Throwable $e) {
            throw new FailToPublishCommentException(sprintf('Exception thrown during comment publish : "%s".', $e->getMessage()));
        }
    }
}
