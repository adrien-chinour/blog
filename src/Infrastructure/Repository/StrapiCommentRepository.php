<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Social\CommentRepository;
use App\Domain\Social\Exception\FailToPublishCommentException;
use App\Infrastructure\External\Strapi\Http\StrapiApiClient;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiCommentFactory;
use DateTimeImmutable;
use Throwable;

final readonly class StrapiCommentRepository implements CommentRepository
{
    public function __construct(
        private StrapiApiClient $client,
        private StrapiCommentFactory $factory
    ) {}

    public function getArticleComments(string $articleIdentifier): array
    {
        return array_map(
            $this->factory->createFromModel(...),
            $this->client->getArticleComments($articleIdentifier),
        );
    }

    public function createComment(string $articleIdentifier, string $username, string $message, DateTimeImmutable $publishedAt): void
    {
        try {
            $this->client->postComment($articleIdentifier, $username, $message, $publishedAt);
        } catch (Throwable $throwable) {
            throw new FailToPublishCommentException(sprintf('Fail to publish comment on article %s', $articleIdentifier), $throwable->getCode(), previous: $throwable);
        }
    }
}
