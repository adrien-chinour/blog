<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Http;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ContentfulApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $contentfulClient,
        private readonly string $contentfulSpaceId
    ) {
        $this->logger = new NullLogger();
    }

    public function query(string $query): array
    {
        try {
            $options = [
                'body' => ['query' => $query],
            ];

            $response = $this->contentfulClient
                ->request('POST', sprintf('/content/v1/spaces/%s', $this->contentfulSpaceId), $options)
                ->toArray();
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), ['exception' => $e]);

            throw $e;
        }

        return $response['data'] ?? [];
    }
}
