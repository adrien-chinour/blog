<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Baserow\Http;

use App\Infrastructure\External\Baserow\Model\BaserowModelInterface;
use App\Infrastructure\External\Baserow\Model\Comment;
use App\Infrastructure\External\Baserow\Model\Feature;
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
        private readonly string $baserowFeatureTable,
    ) {}

    public function getComments(string $articleIdentifier): array
    {
        return $this->getList(Comment::class, [
            'filter__article_id__equal' => $articleIdentifier,
            'filter__moderated__not_equal' => true,
        ]);
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

    public function getFeatures(): array
    {
        return $this->getList(Feature::class);
    }

    /**
     * Abstraction to query multiple row with only model class and extra filters (query params)
     * @param class-string $class
     */
    private function getList(string $class, array $filters = []): array
    {
        if (false === in_array(BaserowModelInterface::class, class_implements($class) ?: [])) {
            throw new \InvalidArgumentException(sprintf('%s must implement %s.', $class, BaserowModelInterface::class));
        }

        $table = match ($class) {
            Feature::class => $this->baserowFeatureTable,
            Comment::class => $this->baserowCommentTable,
            default => throw new \InvalidArgumentException(sprintf('%s is not a valid model class for Baserow API', $class)),
        };

        try {
            $response = $this->baserowClient->request(
                method: 'GET',
                url: sprintf('/api/database/rows/table/%s/', $table),
                options: [
                    'query' => [
                        'user_field_names' => true,
                        ...$filters
                    ],
                ]
            );

            $results = $this->serializer->deserialize(
                data: json_encode($response->toArray()['results'] ?? []),
                type: $class . '[]',
                format: 'json',
            );
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), ['exception' => $e]);
            $results = [];
        }

        Assert::isArray($results);
        Assert::allIsInstanceOf($results, $class);
        Assert::allImplementsInterface($results, BaserowModelInterface::class);

        return array_map(fn (BaserowModelInterface $result) => $result->toDomain(), $results);
    }
}
