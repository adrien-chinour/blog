<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Social;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Social\CommentValidator;
use App\Domain\Social\Exception\InvalidCommentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CommentValidatorTest extends TestCase
{
    private ValidatorInterface&MockObject $symfonyValidator;
    private CommentValidator $validator;

    protected function setUp(): void
    {
        $this->symfonyValidator = $this->createMock(ValidatorInterface::class);
        $this->validator = new CommentValidator($this->symfonyValidator);
    }

    // Username Validation Tests

    public function testValidateUsernameWithValidUsername(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Should not throw exception
        $this->validator->validateUsername('john_doe');
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function testValidateUsernameThrowsExceptionWhenBlank(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Username cannot be blank',
            'Username cannot be blank',
            [],
            null,
            '',
            ''
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');

        $this->validator->validateUsername('');
    }

    public function testValidateUsernameThrowsExceptionWhenTooShort(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Username must be at least 2 characters long',
            'Username must be at least 2 characters long',
            [],
            null,
            '',
            'a'
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');

        $this->validator->validateUsername('a');
    }

    public function testValidateUsernameThrowsExceptionWhenTooLong(): void
    {
        $longUsername = str_repeat('a', 51);
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Username cannot be longer than 50 characters',
            'Username cannot be longer than 50 characters',
            [],
            null,
            '',
            $longUsername
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');

        $this->validator->validateUsername($longUsername);
    }

    public function testValidateUsernameThrowsExceptionWhenStartsWithWhitespace(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');
        $this->expectExceptionMessage('Username cannot start or end with whitespace');

        $this->validator->validateUsername(' username');
    }

    public function testValidateUsernameThrowsExceptionWhenEndsWithWhitespace(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');
        $this->expectExceptionMessage('Username cannot start or end with whitespace');

        $this->validator->validateUsername('username ');
    }

    public function testValidateUsernameThrowsExceptionWhenContainsInvalidCharacters(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Username contains invalid characters',
            'Username contains invalid characters',
            [],
            null,
            '',
            'user@name'
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid username');

        $this->validator->validateUsername('user@name');
    }

    // Message Validation Tests

    public function testValidateMessageWithValidMessage(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Should not throw exception
        $this->validator->validateMessage('This is a valid message');
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function testValidateMessageThrowsExceptionWhenBlank(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Message cannot be blank',
            'Message cannot be blank',
            [],
            null,
            '',
            ''
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');

        $this->validator->validateMessage('');
    }

    public function testValidateMessageThrowsExceptionWhenOnlyWhitespace(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('Message cannot be blank');

        $this->validator->validateMessage('   ');
    }

    public function testValidateMessageThrowsExceptionWhenTooShort(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Message must be at least 3 characters long',
            'Message must be at least 3 characters long',
            [],
            null,
            '',
            'ab'
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');

        $this->validator->validateMessage('ab');
    }

    public function testValidateMessageThrowsExceptionWhenTooLong(): void
    {
        $longMessage = str_repeat('a', 2001);
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Message cannot be longer than 2000 characters',
            'Message cannot be longer than 2000 characters',
            [],
            null,
            '',
            $longMessage
        ));

        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');

        $this->validator->validateMessage($longMessage);
    }

    public function testValidateMessageThrowsExceptionWhenTooManyLines(): void
    {
        $message = str_repeat("line\n", 51);
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('too many lines');

        $this->validator->validateMessage($message);
    }

    public function testValidateMessageThrowsExceptionWhenContainsScriptTag(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('potentially dangerous content');

        $this->validator->validateMessage('Hello <script>alert("xss")</script>');
    }

    public function testValidateMessageThrowsExceptionWhenContainsJavaScriptProtocol(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('potentially dangerous content');

        $this->validator->validateMessage('Check this: javascript:alert("xss")');
    }

    public function testValidateMessageThrowsExceptionWhenContainsEventHandlers(): void
    {
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('potentially dangerous content');

        $this->validator->validateMessage('Test onclick="alert(1)"');
    }

    public function testValidateMessageThrowsExceptionWhenContainsTooManyUrls(): void
    {
        $message = 'Check these: http://example.com http://test.com http://demo.com http://spam.com';
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Invalid message');
        $this->expectExceptionMessage('too many URLs');

        $this->validator->validateMessage($message);
    }

    public function testValidateMessageAcceptsValidUrls(): void
    {
        $message = 'Check these: http://example.com https://test.com';
        $this->symfonyValidator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Should not throw exception (2 URLs is within limit of 3)
        $this->validator->validateMessage($message);
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    // Article Validation Tests

    public function testValidateArticleWithValidPublishedArticle(): void
    {
        $articleRepository = $this->createMock(BlogArticleRepository::class);
        $article = $this->createMock(BlogArticle::class);

        $articleRepository->expects($this->once())
            ->method('getById')
            ->with('article-id', true)
            ->willReturn($article);

        // Should not throw exception
        $this->validator->validateArticle($articleRepository, 'article-id');
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function testValidateArticleThrowsExceptionWhenArticleNotFound(): void
    {
        $articleRepository = $this->createMock(BlogArticleRepository::class);

        $articleRepository->expects($this->once())
            ->method('getById')
            ->with('non-existent-id', true)
            ->willReturn(null);

        $this->expectException(InvalidCommentException::class);
        $this->expectExceptionMessage('Article with identifier "non-existent-id" does not exist or is not published');

        $this->validator->validateArticle($articleRepository, 'non-existent-id');
    }
}

