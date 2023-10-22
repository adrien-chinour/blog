<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Http;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ContentfulApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly HttpClientInterface $contentfulClient)
    {
        $this->logger = new NullLogger();
    }

    public function query(string $query): array
    {
        try {
            $options = [
                'body' => ['query' => $query],
            ];

            $response = $this->contentfulClient->request('POST', '/content/v1/spaces/0c7qlubj8id5', $options)->toArray();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return [];
        }

        return $response['data'] ?? [];
    }
}
