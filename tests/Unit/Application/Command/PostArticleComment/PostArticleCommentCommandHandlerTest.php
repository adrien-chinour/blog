<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\PostArticleComment;

use App\Application\Command\PostArticleComment\PostArticleCommentCommand;
use App\Application\Command\PostArticleComment\PostArticleCommentCommandHandler;
use App\Domain\Social\CommentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PostArticleCommentCommandHandlerTest extends TestCase
{
    private CommentRepository&MockObject $commentRepository;
    private PostArticleCommentCommandHandler $handler;

    public function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->handler = new PostArticleCommentCommandHandler($this->commentRepository);
    }

    public function testCreateCommentWillCreateCommentOnRepository()
    {
        $command = new PostArticleCommentCommand('id', 'username', 'message');

        $this->commentRepository->expects($this->once())
            ->method('createComment')
            ->with(
                $command->articleIdentifier,
                $command->username,
                $command->message,
                $command->publishedAt
            );
        
        ($this->handler)($command);
    }
}
