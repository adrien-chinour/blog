<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Article;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ListArticleControllerTest extends WebTestCase
{
    public function testListArticleWillReturnCollectionOfArticle(): void
    {
        $client = self::createClient();

        $client->request('GET', '/articles');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testListArticleWithLimitWillReturnCollectionOfArticleWithSameLimit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/articles', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $payload = json_decode($response->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);
    }

    /**
     * @dataProvider invalidLimits
     */
    public function testListArticleWithInvalidLimitWillReturnBadRequest(string $limit): void
    {
        $client = static::createClient();
        $client->request('GET', '/articles', ['limit' => $limit]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public static function invalidLimits(): iterable
    {
        yield 'zero' => ['0'];
        yield 'negative' => ['-1'];
        yield 'too large' => ['1000'];
        yield 'not a number' => ['abc'];
    }
}
