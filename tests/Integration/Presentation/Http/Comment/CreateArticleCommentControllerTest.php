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
                'message' => 'Super message',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // Username validation tests

    public function testCreateArticleCommentWithUsernameTooShortWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'a',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Username must be at least', $response['error']);
    }

    public function testCreateArticleCommentWithUsernameTooLongWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => str_repeat('a', 51),
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Username cannot be longer than', $response['error']);
    }

    public function testCreateArticleCommentWithUsernameWithInvalidCharactersWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'user@name',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('invalid characters', $response['error']);
    }

    public function testCreateArticleCommentWithUsernameWithLeadingWhitespaceWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => ' username',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('cannot start or end with whitespace', $response['error']);
    }

    public function testCreateArticleCommentWithUsernameWithTrailingWhitespaceWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'username ',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('cannot start or end with whitespace', $response['error']);
    }

    public function testCreateArticleCommentWithValidUnicodeUsernameWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'José García',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateArticleCommentWithUsernameAtMinLengthWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'ab',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateArticleCommentWithUsernameAtMaxLengthWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => str_repeat('a', 50),
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // Message validation tests

    public function testCreateArticleCommentWithMessageTooShortWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'ab',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Message must be at least', $response['error']);
    }

    public function testCreateArticleCommentWithMessageTooLongWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => str_repeat('a', 2001),
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Message cannot be longer than', $response['error']);
    }

    public function testCreateArticleCommentWithMessageTooManyLinesWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => str_repeat("line\n", 51),
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('too many lines', $response['error']);
    }

    public function testCreateArticleCommentWithMessageContainingScriptTagWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Valid message <script>alert("xss")</script>',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('dangerous content', $response['error']);
    }

    public function testCreateArticleCommentWithMessageContainingJavaScriptProtocolWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Valid message javascript:alert("xss")',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('dangerous content', $response['error']);
    }

    public function testCreateArticleCommentWithMessageContainingEventHandlersWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Valid message onclick="alert(\'xss\')"',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('dangerous content', $response['error']);
    }

    public function testCreateArticleCommentWithMessageContainingIframeWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Valid message <iframe src="evil.com"></iframe>',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('dangerous content', $response['error']);
    }

    public function testCreateArticleCommentWithMessageContainingTooManyUrlsWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Check these: https://example.com https://test.com https://demo.com https://spam.com',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('too many URLs', $response['error']);
    }

    public function testCreateArticleCommentWithMessageAtMinLengthWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'abc',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateArticleCommentWithMessageAtMaxLengthWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => str_repeat('a', 2000),
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateArticleCommentWithMessageWithValidUrlsWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Check these: https://example.com https://test.com https://demo.com',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateArticleCommentWithMessageWithSpecialCharactersWillSucceed(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => 'Valid message with émojis 🎉 and special chars: !@#$%^&*()',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // Article validation tests

    public function testCreateArticleCommentWithNonExistentArticleWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '99999',
                'username' => 'validuser',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('does not exist or is not published', $response['error']);
    }

    // Edge cases

    public function testCreateArticleCommentWithEmptyUsernameWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => '',
                'message' => 'Valid message',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateArticleCommentWithEmptyMessageWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => '',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('cannot be blank', $response['error']);
    }

    public function testCreateArticleCommentWithWhitespaceOnlyMessageWillReturnBadRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            'POST',
            '/comments',
            parameters: [
                'articleId' => '1',
                'username' => 'validuser',
                'message' => '   ',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('cannot be blank', $response['error']);
    }
}
