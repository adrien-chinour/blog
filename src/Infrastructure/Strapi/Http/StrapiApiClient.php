<?php

declare(strict_types=1);

namespace App\Infrastructure\Strapi\Http;

use App\Infrastructure\Strapi\Model\ContentType\PageContentType;
use App\Infrastructure\Strapi\Model\Core\DocumentCollection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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

            /** @var DocumentCollection $collection */
            $collection = $this->serializer->deserialize($response->getContent(), DocumentCollection::class, JsonEncoder::FORMAT);
            Assert::isInstanceOf($collection, DocumentCollection::class);

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
}
