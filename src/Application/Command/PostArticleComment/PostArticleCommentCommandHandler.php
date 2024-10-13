<?php

declare(strict_types=1);

namespace App\Application\Command\PostArticleComment;

use App\Domain\Social\CommentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PostArticleCommentCommandHandler
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    public function __invoke(PostArticleCommentCommand $command): void
    {
        $this->commentRepository->createComment(
            articleIdentifier: $command->articleIdentifier,
            username: $command->username,
            message: $command->message,
            publishedAt: $command->publishedAt,
        );
    }
}
