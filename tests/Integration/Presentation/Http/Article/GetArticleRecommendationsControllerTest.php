<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetArticleRecommendationsControllerTest extends WebTestCase
{
    public function testGetArticleRecommendationsWillReturnCollectionOfJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1/recommendations');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleRecommendationsWillReturnNotFoundOnUnknownId(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/5/recommendations');

        $this->assertResponseStatusCodeSame(404);
    }
}
