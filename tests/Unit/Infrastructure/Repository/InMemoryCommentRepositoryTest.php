<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Social\Comment;
use App\Infrastructure\Repository\InMemoryCommentRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class InMemoryCommentRepositoryTest extends TestCase
{
    private InMemoryCommentRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryCommentRepository();
    }

    public function testGetArticleCommentsReturnsEmptyArrayWhenNoComments(): void
    {
        $comments = $this->repository->getArticleComments('article-123');

        $this->assertIsArray($comments);
        $this->assertEmpty($comments);
    }

    public function testCreateCommentAddsCommentToRepository(): void
    {
        $publishedAt = new DateTimeImmutable();
        $this->repository->createComment('article-123', 'john_doe', 'Great article!', $publishedAt);

        $comments = $this->repository->getArticleComments('article-123');

        $this->assertCount(1, $comments);
        $this->assertInstanceOf(Comment::class, $comments[0]);
        $this->assertSame('john_doe', $comments[0]->username);
        $this->assertSame('Great article!', $comments[0]->message);
    }

    public function testCreateCommentAddsMultipleComments(): void
    {
        $publishedAt = new DateTimeImmutable();
        $this->repository->createComment('article-123', 'user1', 'First comment', $publishedAt);
        $this->repository->createComment('article-123', 'user2', 'Second comment', $publishedAt);

        $comments = $this->repository->getArticleComments('article-123');

        $this->assertCount(2, $comments);
        $this->assertSame('user1', $comments[0]->username);
        $this->assertSame('user2', $comments[1]->username);
    }

    public function testCreateCommentSeparatesCommentsByArticle(): void
    {
        $publishedAt = new DateTimeImmutable();
        $this->repository->createComment('article-1', 'user1', 'Comment 1', $publishedAt);
        $this->repository->createComment('article-2', 'user2', 'Comment 2', $publishedAt);

        $comments1 = $this->repository->getArticleComments('article-1');
        $comments2 = $this->repository->getArticleComments('article-2');

        $this->assertCount(1, $comments1);
        $this->assertCount(1, $comments2);
        $this->assertSame('user1', $comments1[0]->username);
        $this->assertSame('user2', $comments2[0]->username);
    }

    public function testCreateCommentGeneratesUniqueIds(): void
    {
        $publishedAt = new DateTimeImmutable();
        $this->repository->createComment('article-123', 'user1', 'Comment 1', $publishedAt);
        $this->repository->createComment('article-123', 'user2', 'Comment 2', $publishedAt);

        $comments = $this->repository->getArticleComments('article-123');

        $this->assertNotSame($comments[0]->id, $comments[1]->id);
    }

    public function testGetArticleCommentsWithPreloadedComments(): void
    {
        $publishedAt = new DateTimeImmutable();
        $preloadedComments = [
            'article-123' => [
                new Comment('comment-1', 'user1', 'Preloaded comment', $publishedAt),
            ],
        ];

        $repository = new InMemoryCommentRepository($preloadedComments);
        $comments = $repository->getArticleComments('article-123');

        $this->assertCount(1, $comments);
        $this->assertSame('comment-1', $comments[0]->id);
    }
}

