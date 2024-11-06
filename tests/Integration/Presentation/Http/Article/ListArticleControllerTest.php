<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListArticleControllerTest extends WebTestCase
{
    public function testListArticleWillReturnCollectionOfArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testListArticleWithLimitWillReturnCollectionOfArticleWithSameLimit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/articles', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $payload = json_decode($response->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);
    }
}
