<?php

declare(strict_types=1);

namespace App\Application\Command\CreateComment;

final readonly class CreateCommentCommand
{
    public function __construct(
        public string $author,
        public string $comment,
        public string $articleIdentifier,
    ) {}
}
