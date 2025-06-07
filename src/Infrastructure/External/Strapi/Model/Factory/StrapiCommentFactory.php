<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Social\Comment;
use App\Infrastructure\External\Strapi\Model\ContentType\CommentContentType;

final readonly class StrapiCommentFactory
{
    public function createFromModel(CommentContentType $comment): Comment
    {
        return new Comment(
            id: (string)$comment->id,
            username: $comment->username,
            message: $comment->message,
            publishedAt: $comment->publishedAt
        );
    }
}
