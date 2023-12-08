<?php

namespace App\Infrastructure;

use App\Domain\Social\Comment;
use App\Domain\Social\CommentRepository;

final class MockCommentRepository implements CommentRepository
{
    public function getByArticle(string $identifier): array
    {
        return [
            Comment::create(
                'Nayan Pahuja',
                'Thanks for writing this beautiful article. I had been reading about and working with some practical kubernetes for a while now and this article just humbles me to my core about how simplified yet complete one can write. Great Read',
                'f'
            ),
            Comment::create(
                'Prayson Wilfred Daniel ',
                'Unique and brilliant explanation of what Kubernetes is. This is how we ought to explain complex ideas in relatable paradigms. 👏',
                'f'
            ),
        ];
    }

    public function save(Comment $comment): void {}
}
