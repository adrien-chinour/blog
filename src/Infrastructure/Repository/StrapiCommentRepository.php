<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Social\CommentRepository;
use App\Infrastructure\External\Strapi\Http\StrapiApiClient;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiCommentFactory;
use DateTimeImmutable;

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
        $this->client->postComment($articleIdentifier, $username, $message, $publishedAt);
    }
}
