<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetArticleBySlugControllerTest extends WebTestCase
{
    public function testGetArticleBySlugWillReturnJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/construire-un-site-statique-avec-php');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleWillReturnNotFoundOnUnknownSlug(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/unknown-slug');

        $this->assertResponseStatusCodeSame(404);
    }
}
