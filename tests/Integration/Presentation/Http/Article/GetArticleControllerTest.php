<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use const App\Tests\Integration\TEST_ARTICLE_ID;

final class GetArticleControllerTest extends WebTestCase
{
    public function testGetArticleWillReturnJsonArticle(): void
    {
        $client = static::createClient();

        // FIXME use a better way to test with real data
        $client->request('GET', sprintf('/articles/%s', '4aAkSjsn311n0jwAgNnIvH'));

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleWillReturnNotFoundOnUnknownId(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', sprintf('/articles/%s', 'unknown'));

        $this->assertResponseStatusCodeSame(404);

    }
}
