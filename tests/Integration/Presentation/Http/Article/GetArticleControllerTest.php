<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetArticleControllerTest extends WebTestCase
{
    public function testGetArticleWillReturnJsonArticle(): void
    {
        $client = self::createClient();

        $client->request('GET', '/articles/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
        $this->assertSame('1', json_decode($client->getResponse()->getContent(), true)['id']);
    }

    public function testGetArticleWillReturnNotFoundOnUnknownId(): void
    {
        $client = self::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/5');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetArticleBySlugWillReturnJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/construire-un-site-statique-avec-php');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
        $this->assertSame('2', json_decode($client->getResponse()->getContent(), true)['id']);
    }

    public function testGetArticleBySlugWithoutDashWillReturnJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/docker');

        $this->assertResponseIsSuccessful();
        $this->assertSame('3', json_decode($client->getResponse()->getContent(), true)['id']);
    }

    public function testGetArticleWillReturnNotFoundOnUnknownSlug(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/articles/unknown-slug');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetArticleSerializesRecommendationsAsList(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('"recommendations":["2","unknown-article"]', $client->getResponse()->getContent());
    }

    public function testGetArticlePreviewIsForbiddenToAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1', ['published' => 'false']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetArticlePreviewIsAllowedToAdmin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1', ['published' => 'false'], server: ['HTTP_AUTHORIZATION' => 'Bearer TestToken']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
        $this->assertResponseHeaderNotSame('Cache-Control', 'max-age=60, public, s-maxage=120');
    }
}
