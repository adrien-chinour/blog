<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetArticleControllerTest extends WebTestCase
{
    public function testGetArticleWillReturnJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleWillReturnNotFoundOnUnknownId(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/5');

        $this->assertResponseStatusCodeSame(404);

    }
}
