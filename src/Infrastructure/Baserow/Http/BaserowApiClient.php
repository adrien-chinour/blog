<?php

declare(strict_types=1);

namespace App\Infrastructure\Baserow\Http;

use App\Infrastructure\Baserow\Model\Comment;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class BaserowApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $baserowClient,
        private readonly SerializerInterface $serializer,
        private readonly string $baserowCommentTable,
    ) {}

    public function getComments(string $articleIdentifier): array
    {
        try {
            $response = $this->baserowClient->request(
                method: 'GET',
                url: sprintf('/api/database/rows/table/%s/', $this->baserowCommentTable),
                options: [
                    'query' => [
                        'user_field_names' => true,
                        'filter__article_id__equal' => $articleIdentifier,
                        'filter__moderated__not_equal' => true
                    ]
                ],
            );

            $comments = $this->serializer->deserialize(
                json_encode($response->toArray()['results'] ?? []),
                Comment::class . '[]',
                'json',
            );
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), ['exception' => $e]);
            $comments = [];
        }

        Assert::isArray($comments);
        Assert::allIsInstanceOf($comments, Comment::class);

        return array_map(fn (Comment $comment) => $comment->toDomain(), $comments);
    }

    public function postComment(string $articleIdentifier, string $username, string $message, \DateTimeImmutable $publishedAt): void
    {
        $response = $this->baserowClient->request(
            method: 'POST',
            url: sprintf('/api/database/rows/table/%s/', $this->baserowCommentTable),
            options: [
                'query' => [
                    'user_field_names' => true,
                ],
                'body' => $this->serializer->serialize(
                    data: new Comment($articleIdentifier, $message, $username, $publishedAt, false),
                    format: 'json',
                )
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new \HttpException(sprintf('Unexpected status code %d', $response->getStatusCode()));
        }
    }
}
