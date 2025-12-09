<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Social\Comment;
use App\Infrastructure\External\Strapi\Model\ContentType\CommentContentType;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiCommentFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class StrapiCommentFactoryTest extends TestCase
{
    private StrapiCommentFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StrapiCommentFactory();
    }

    public function testCreateFromModelCreatesComment(): void
    {
        $commentContentType = $this->createCommentContentType(123, 'John Doe', 'Great article!', new DateTimeImmutable('2024-01-15'));

        $comment = $this->factory->createFromModel($commentContentType);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertSame('123', $comment->id);
        $this->assertSame('John Doe', $comment->username);
        $this->assertSame('Great article!', $comment->message);
        $this->assertInstanceOf(DateTimeImmutable::class, $comment->publishedAt);
        $this->assertSame('2024-01-15', $comment->publishedAt->format('Y-m-d'));
    }

    public function testCreateFromModelConvertsIdToString(): void
    {
        $commentContentType = $this->createCommentContentType(456, 'Jane Doe', 'Nice post', new DateTimeImmutable('2024-02-20'));

        $comment = $this->factory->createFromModel($commentContentType);

        $this->assertSame('456', $comment->id);
        $this->assertIsString($comment->id);
    }

    private function createCommentContentType(int $id, string $username, string $message, DateTimeImmutable $publishedAt): CommentContentType
    {
        $comment = new CommentContentType();
        $comment->id = $id;
        $comment->documentId = 'doc-' . $id;
        $comment->createdAt = $publishedAt;
        $comment->updatedAt = $publishedAt;
        $comment->publishedAt = $publishedAt;
        $comment->username = $username;
        $comment->articleId = 'article-123';
        $comment->message = $message;

        return $comment;
    }
}

