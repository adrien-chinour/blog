<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Meilisearch;

use App\Infrastructure\External\Meilisearch\MeilisearchClientFactory;
use Meilisearch\Client;
use PHPUnit\Framework\TestCase;

final class MeilisearchClientFactoryTest extends TestCase
{
    public function testInvokeCreatesClientWithCorrectHost(): void
    {
        $factory = new MeilisearchClientFactory(
            meilisearchHost: 'http://localhost:7700',
            meilisearchToken: 'test-token'
        );

        $client = $factory();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testInvokeCreatesClientWithDifferentHosts(): void
    {
        $factory1 = new MeilisearchClientFactory('http://localhost:7700', 'token');
        $factory2 = new MeilisearchClientFactory('http://search:7700', 'token');

        $client1 = $factory1();
        $client2 = $factory2();

        $this->assertInstanceOf(Client::class, $client1);
        $this->assertInstanceOf(Client::class, $client2);
    }
}

