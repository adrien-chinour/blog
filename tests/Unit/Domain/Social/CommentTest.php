<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Social;

use App\Domain\Social\Comment;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testCommentCreation(): void
    {
        $publishedAt = new DateTimeImmutable('2024-01-15 10:30:00');
        $comment = new Comment(
            id: 'comment-123',
            username: 'john_doe',
            message: 'Great article!',
            publishedAt: $publishedAt
        );

        $this->assertSame('comment-123', $comment->id);
        $this->assertSame('john_doe', $comment->username);
        $this->assertSame('Great article!', $comment->message);
        $this->assertSame($publishedAt, $comment->publishedAt);
    }

    public function testCommentPropertiesArePublic(): void
    {
        $publishedAt = new DateTimeImmutable();
        $comment = new Comment(
            id: 'comment-123',
            username: 'john_doe',
            message: 'Test message',
            publishedAt: $publishedAt
        );

        $this->assertIsString($comment->id);
        $this->assertIsString($comment->username);
        $this->assertIsString($comment->message);
        $this->assertInstanceOf(DateTimeImmutable::class, $comment->publishedAt);
    }
}

