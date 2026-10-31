<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Http;

use App\Infrastructure\External\Contentful\Http\Exception\RequestFailedException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ContentfulApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $contentfulClient,
        private readonly string $contentfulSpaceId
    ) {
        $this->logger = new NullLogger();
    }

    /**
     * @return array the "data" part of the GraphQL response
     * @throws Throwable on transport failure or when Contentful returns no data at all
     */
    public function query(string $query): array
    {
        try {
            $options = [
                'json' => ['query' => $query],
            ];

            $response = $this->contentfulClient
                ->request('POST', sprintf('/content/v1/spaces/%s', $this->contentfulSpaceId), $options)
                ->toArray();
        } catch (Throwable $e) {
            $this->logger?->error($e->getMessage(), ['exception' => $e]);

            throw $e;
        }

        if (isset($response['errors'])) {
            $this->logger?->error('Contentful GraphQL query returned errors', ['errors' => $response['errors']]);
        }

        // Partial results (data + errors) are tolerated, a response without data is a failure
        if (!is_array($response['data'] ?? null)) {
            throw new RequestFailedException('Contentful GraphQL query returned no data');
        }

        return $response['data'];
    }
}
