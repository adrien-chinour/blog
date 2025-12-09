<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Social\Exception;

use App\Domain\Social\Exception\InvalidCommentException;
use PHPUnit\Framework\TestCase;

final class InvalidCommentExceptionTest extends TestCase
{
    public function testInvalidArticle(): void
    {
        $exception = InvalidCommentException::invalidArticle('article-123');

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertStringContainsString('Article with identifier "article-123"', $exception->getMessage());
        $this->assertStringContainsString('does not exist or is not published', $exception->getMessage());
    }

    public function testInvalidUsername(): void
    {
        $exception = InvalidCommentException::invalidUsername('Username is too short');

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Invalid username: Username is too short', $exception->getMessage());
    }

    public function testInvalidMessage(): void
    {
        $exception = InvalidCommentException::invalidMessage('Message contains invalid content');

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Invalid message: Message contains invalid content', $exception->getMessage());
    }

    public function testMessageTooLong(): void
    {
        $exception = InvalidCommentException::messageTooLong(2500, 2000);

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Message is too long: 2500 characters (max: 2000)', $exception->getMessage());
    }

    public function testUsernameTooLong(): void
    {
        $exception = InvalidCommentException::usernameTooLong(60, 50);

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Username is too long: 60 characters (max: 50)', $exception->getMessage());
    }

    public function testMessageTooShort(): void
    {
        $exception = InvalidCommentException::messageTooShort(1, 3);

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Message is too short: 1 characters (min: 3)', $exception->getMessage());
    }

    public function testUsernameTooShort(): void
    {
        $exception = InvalidCommentException::usernameTooShort(1, 2);

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Username is too short: 1 characters (min: 2)', $exception->getMessage());
    }

    public function testContainsProhibitedContent(): void
    {
        $exception = InvalidCommentException::containsProhibitedContent('Script tags detected');

        $this->assertInstanceOf(InvalidCommentException::class, $exception);
        $this->assertSame('Content contains prohibited material: Script tags detected', $exception->getMessage());
    }
}

