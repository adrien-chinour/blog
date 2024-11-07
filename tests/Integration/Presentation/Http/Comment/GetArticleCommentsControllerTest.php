<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Comment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetArticleCommentsControllerTest extends WebTestCase
{
    public function testGetArticleCommentsWillReturnCollectionOfJsonArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/1/comments');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }
}
