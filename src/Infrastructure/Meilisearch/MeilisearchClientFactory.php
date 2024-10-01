<?php

declare(strict_types=1);

namespace App\Infrastructure\Meilisearch;

use Meilisearch\Client;

final readonly class MeilisearchClientFactory
{
    public function __construct(
        private string $meilisearchHost,
        private string $meilisearchToken,
    ) {}

    public function __invoke(): Client
    {
        return new Client($this->meilisearchHost, $this->meilisearchToken);
    }
}
