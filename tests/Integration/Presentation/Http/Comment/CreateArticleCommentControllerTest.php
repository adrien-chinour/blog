<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Comment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateArticleCommentControllerTest extends WebTestCase
{
    public function testCreateArticleCommentWithBadPayloadWillThrowBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/comments');
        $this->assertResponseIsUnprocessable();
    }

    public function testCreateArticleCommentWithCorrectPayloadWillCreateComment(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'adrien',
                'message' => 'ok',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
