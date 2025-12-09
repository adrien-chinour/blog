<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\PostArticleComment;

use App\Application\Command\PostArticleComment\PostArticleCommentCommand;
use App\Application\Command\PostArticleComment\PostArticleCommentCommandHandler;
use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Social\CommentRepository;
use App\Domain\Social\CommentSanitizer;
use App\Domain\Social\CommentValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostArticleCommentCommandHandlerTest extends TestCase
{
    private CommentRepository&MockObject $commentRepository;
    private BlogArticleRepository&MockObject $articleRepository;
    private ValidatorInterface&MockObject $symfonyValidator;
    private CommentValidator $validator;
    private CommentSanitizer&MockObject $sanitizer;
    private PostArticleCommentCommandHandler $handler;

    public function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->articleRepository = $this->createMock(BlogArticleRepository::class);
        $this->symfonyValidator = $this->createMock(ValidatorInterface::class);
        $this->validator = new CommentValidator($this->symfonyValidator);
        $this->sanitizer = $this->createMock(CommentSanitizer::class);

        $this->handler = new PostArticleCommentCommandHandler(
            $this->commentRepository,
            $this->articleRepository,
            $this->validator,
            $this->sanitizer,
        );
    }

    public function testCreateCommentWillCreateCommentOnRepository(): void
    {
        $command = new PostArticleCommentCommand('article-id', 'testuser', 'Test message');

        // Mock article exists (validator will call repository)
        $article = $this->createMock(BlogArticle::class);
        $this->articleRepository->expects($this->once())
            ->method('getById')
            ->with('article-id', true)
            ->willReturn($article);

        // Mock Symfony Validator to return no violations (validation passes)
        $this->symfonyValidator->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Mock sanitization
        $this->sanitizer->expects($this->once())
            ->method('sanitizeUsername')
            ->with('testuser')
            ->willReturn('testuser');
        $this->sanitizer->expects($this->once())
            ->method('sanitizeMessage')
            ->with('Test message')
            ->willReturn('Test message');

        // Expect comment creation with sanitized data
        $this->commentRepository->expects($this->once())
            ->method('createComment')
            ->with(
                'article-id',
                'testuser',
                'Test message',
                $command->publishedAt
            );

        ($this->handler)($command);
    }
}
