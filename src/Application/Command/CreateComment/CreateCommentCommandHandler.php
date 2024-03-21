<?php

declare(strict_types=1);

namespace App\Application\Command\CreateComment;

use App\Domain\Social\Comment;
use App\Domain\Social\CommentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateCommentCommandHandler
{
    public function __construct(public CommentRepository $commentRepository) {}

    public function __invoke(CreateCommentCommand $command): void
    {
        $this->commentRepository->save(
            Comment::create($command->author, $command->comment, $command->articleIdentifier)
        );
    }
}
