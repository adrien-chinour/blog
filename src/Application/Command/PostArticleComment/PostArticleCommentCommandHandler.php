<?php

declare(strict_types=1);

namespace App\Application\Command\PostArticleComment;

use App\Application\Exception\BadRequestException;
use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Social\CommentRepository;
use App\Domain\Social\CommentSanitizer;
use App\Domain\Social\CommentValidator;
use App\Domain\Social\Exception\InvalidCommentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PostArticleCommentCommandHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private CommentRepository $commentRepository,
        private BlogArticleRepository $articleRepository,
        private CommentValidator $validator,
        private CommentSanitizer $sanitizer,
    ) {}

    public function __invoke(PostArticleCommentCommand $command): void
    {
        try {
            // Validate article exists and is published (Domain rule)
            $this->validator->validateArticle($this->articleRepository, $command->articleIdentifier);

            // Validate username (Domain rule)
            $this->validator->validateUsername($command->username);

            // Validate message (Domain rule)
            $this->validator->validateMessage($command->message);
        } catch (InvalidCommentException $e) {
            // Convert domain exception to application exception
            throw BadRequestException::create($e->getMessage());
        }

        // Sanitize inputs (Domain service)
        $sanitizedUsername = $this->sanitizer->sanitizeUsername($command->username);
        $sanitizedMessage = $this->sanitizer->sanitizeMessage($command->message);

        // Log comment submission for audit purposes
        $this->logger?->info('Comment submitted', [
            'articleIdentifier' => $command->articleIdentifier,
            'username' => $sanitizedUsername,
            'messageLength' => mb_strlen($sanitizedMessage),
        ]);

        // Create comment with sanitized data
        $this->commentRepository->createComment(
            articleIdentifier: $command->articleIdentifier,
            username: $sanitizedUsername,
            message: $sanitizedMessage,
            publishedAt: $command->publishedAt,
        );
    }
}
