<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetArticleRecommendationsControllerTest extends WebTestCase
{
    public function testGetArticleRecommendationsWillReturnCollectionOfJsonArticle(): void
    {
        $client = self::createClient();

        $client->request('GET', '/articles/1/recommendations');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleRecommendationsSkipsUnknownRecommendedArticles(): void
    {
        $client = self::createClient();

        // Article 1 recommends article 2 and an article that does not exist anymore
        $client->request('GET', '/articles/1/recommendations');

        $this->assertResponseIsSuccessful();
        $payload = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $payload);
        $this->assertSame('2', $payload[0]['id']);
    }

    public function testGetArticleRecommendationsFallbacksOnLatestArticles(): void
    {
        $client = self::createClient();

        // Article 2 has no explicit recommendation
        $client->request('GET', '/articles/2/recommendations');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, json_decode($client->getResponse()->getContent(), true));
    }

    public function testGetArticleRecommendationsWillReturnNotFoundOnUnknownId(): void
    {
        $client = self::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/5/recommendations');

        $this->assertResponseStatusCodeSame(404);
    }
}
