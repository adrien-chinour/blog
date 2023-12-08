<?php

declare(strict_types=1);

namespace App\Domain\Social;

interface CommentRepository
{
    /**
     * @return Comment[]
     */
    public function getByArticle(string $identifier): array;

    public function save(Comment $comment): void;
}
