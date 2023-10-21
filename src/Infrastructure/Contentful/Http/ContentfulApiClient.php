<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ContentfulApiClient
{
    public function __construct(private HttpClientInterface $contentfulClient) {}

    public function query(string $query): array
    {
        try {
            $response = $this->contentfulClient->request('POST', '/content/v1/spaces/0c7qlubj8id5', [
                'body' => ['query' => $query],
            ])->toArray();
        } catch (\Throwable $e) {
            dd($e);
            return [];
        }

        return $response['data'] ?? [];
    }
}


