<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Http;

use App\Infrastructure\External\Strapi\Model\ContentType\FeatureContentType;
use App\Infrastructure\External\Strapi\Model\ContentType\PageContentType;
use App\Infrastructure\External\Strapi\Model\Core\DocumentCollection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
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

final class StrapiApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $strapiClient,
        private readonly SerializerInterface $serializer
    ) {
        $this->logger = new NullLogger();
    }

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
                default => throw new \RuntimeException(sprintf('Too much result. Page url must be unique. %d result for path %s', $total, $path)),
            };
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage());

            return null;
        }
    }

    /**
     * @return FeatureContentType[]
     */
    public function getFeatures(): array
    {
        try {
            $response = $this->strapiClient->request('GET', '/api/features');

            $features = array_map(
                fn ($item) => $this->serializer->deserialize(json_encode($item), FeatureContentType::class, JsonEncoder::FORMAT),
                $this->toCollection($response)->data ?? []
            );
            Assert::allIsInstanceOf($features, FeatureContentType::class);
        } catch (Throwable $throwable) {
            $this->logger?->critical($throwable->getMessage());

            return [];
        }

        return $features;
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
