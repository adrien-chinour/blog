<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Http;

use App\Infrastructure\External\Strapi\Model\ContentType\CommentContentType;
use App\Infrastructure\External\Strapi\Model\ContentType\FeatureContentType;
use App\Infrastructure\External\Strapi\Model\ContentType\PageContentType;
use App\Infrastructure\External\Strapi\Model\Core\DocumentCollection;
use DateTimeImmutable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Low level Strapi REST client.
 *
 * Failures are logged and re-thrown on purpose: swallowing them into empty results would let the
 * CacheMiddleware store a transient outage as a legitimate (and long lived) response.
 */
final class StrapiApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Strapi default page size is 25, 100 is the default maximum allowed by Strapi.
     */
    private const int MAX_PAGE_SIZE = 100;

    public function __construct(
        private readonly HttpClientInterface $strapiClient,
        private readonly SerializerInterface $serializer
    ) {
        $this->logger = new NullLogger();
    }

    /**
     * @throws Throwable
     */
    public function getPage(string $path): ?PageContentType
    {
        try {
            $response = $this->strapiClient->request('GET', '/api/pages', [
                'query' => [
                    'filters' => [
                        'url' => [
                            '$eq' => $path,
                        ]
                    ]
                ]
            ]);

            $collection = $this->toCollection($response);

            return match ($total = $collection->meta->pagination->total) {
                0 => null,
                1 => $this->serializer->deserialize(json_encode($collection->data[0]), PageContentType::class, JsonEncoder::FORMAT),
                default => throw new RuntimeException(sprintf('Too much result. Page url must be unique. %d result for path %s', $total, $path)),
            };
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage(), ['exception' => $throwable]);

            throw $throwable;
        }
    }

    /**
     * @return FeatureContentType[]
     * @throws Throwable
     */
    public function getFeatures(): array
    {
        try {
            $response = $this->strapiClient->request('GET', '/api/features');

            $features = array_map(
                fn ($item): mixed => $this->serializer->deserialize(json_encode($item), FeatureContentType::class, JsonEncoder::FORMAT),
                $this->toCollection($response)->data ?? []
            );
            Assert::allIsInstanceOf($features, FeatureContentType::class);
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage(), ['exception' => $throwable]);

            throw $throwable;
        }

        return $features;
    }

    /**
     * @return CommentContentType[]
     * @throws Throwable
     */
    public function getArticleComments(string $articleIdentifier): array
    {
        try {
            $response = $this->strapiClient->request('GET', '/api/comments', [
                'query' => [
                    'filters' => [
                        'article_id' => [
                            '$eq' => $articleIdentifier,
                        ]
                    ],
                    'pagination' => [
                        'pageSize' => self::MAX_PAGE_SIZE,
                    ],
                ]
            ]);

            $comments = array_map(
                fn ($item): mixed => $this->serializer->deserialize(json_encode($item), CommentContentType::class, JsonEncoder::FORMAT),
                $this->toCollection($response)->data ?? []
            );
            Assert::allIsInstanceOf($comments, CommentContentType::class);
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage(), ['exception' => $throwable]);

            throw $throwable;
        }

        return $comments;
    }

    /**
     * @throws Throwable when Strapi is unreachable or does not acknowledge the creation
     */
    public function postComment(string $articleIdentifier, string $username, string $message, DateTimeImmutable $publishedAt): void
    {
        try {
            $response = $this->strapiClient->request('POST', '/api/comments', [
                'json' => [
                    'data' => [
                        'article_id' => $articleIdentifier,
                        'username' => $username,
                        'message' => $message
                    ]
                ]
            ]);

            if (201 !== $statusCode = $response->getStatusCode()) {
                throw new RuntimeException(sprintf('Unexpected status code %d when posting comment', $statusCode));
            }
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage(), ['exception' => $throwable]);

            throw $throwable;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function toCollection(ResponseInterface $response): DocumentCollection
    {
        /** @var DocumentCollection $collection */
        $collection = $this->serializer->deserialize($response->getContent(), DocumentCollection::class, JsonEncoder::FORMAT);
        Assert::isInstanceOf($collection, DocumentCollection::class);

        return $collection;
    }
}
