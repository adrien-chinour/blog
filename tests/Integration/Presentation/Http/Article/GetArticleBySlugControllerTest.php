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

        // FIXME use a better way to test with real data
        $client->request('GET', sprintf('/articles/%s', 'construire-un-site-statique-avec-php'));

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetArticleWillReturnNotFoundOnUnknownSlug(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', sprintf('/articles/%s', 'unknown-slug'));

        $this->assertResponseStatusCodeSame(404);
    }
}
