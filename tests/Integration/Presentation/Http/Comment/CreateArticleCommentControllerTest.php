<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Comment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateArticleCommentControllerTest extends WebTestCase
{
    public function testCreateArticleCommentWithBadPayloadWillThrowBadRequest(): void
    {
        $client = static::createClient();

        $client->request('POST', '/comments');
        $this->assertResponseIsUnprocessable();
    }
}
