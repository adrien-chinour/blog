<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Search;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SearchArticleControllerTest extends WebTestCase
{
    public function testSearchArticleWithoutQueryWillFailed()
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/search/articles');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSearchArticleWithQueryReturnCollectionOfArticle()
    {
        $client = static::createClient();
        $client->request('GET', '/search/articles', ['query' => 'php']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $payload = json_decode($response->getContent(), true);
        $this->assertIsArray($payload);
    }
}
