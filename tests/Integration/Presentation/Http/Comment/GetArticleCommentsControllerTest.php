<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Comment;

use App\Tests\Factory\Repository\InMemoryCommentRepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetArticleCommentsControllerTest extends WebTestCase
{
    public function testGetArticleCommentsWillReturnCollectionOfJsonComment(): void
    {
        $client = self::createClient();

        $client->request('GET', '/articles/1/comments');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $payload = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertCount(InMemoryCommentRepositoryFactory::COMMENTS_ON_FIRST_ARTICLE, $payload);
        foreach ($payload as $comment) {
            $this->assertArrayHasKey('username', $comment);
            $this->assertArrayHasKey('message', $comment);
        }
    }

    public function testGetArticleCommentsWillReturnEmptyListOnArticleWithoutComment(): void
    {
        $client = static::createClient();

        $client->request('GET', '/articles/2/comments');

        $this->assertResponseIsSuccessful();
        $this->assertSame('[]', $client->getResponse()->getContent());
    }
}
