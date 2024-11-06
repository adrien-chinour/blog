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

        // FIXME use a better way to test with real data
        $client->request('GET', sprintf('/articles/%s/recommendations', '4aAkSjsn311n0jwAgNnIvH'));

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleRecommendationsWillReturnNotFoundOnUnknownId(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', sprintf('/articles/%s/recommendations', 'unknown'));

        $this->assertResponseStatusCodeSame(404);
    }
}
