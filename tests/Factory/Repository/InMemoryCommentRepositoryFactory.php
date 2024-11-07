<?php

declare(strict_types=1);

namespace App\Tests\Factory\Repository;

use App\Infrastructure\Repository\InMemoryCommentRepository;
use App\Tests\Factory\CommentFactory;

final readonly class InMemoryCommentRepositoryFactory
{
    public static function create(): InMemoryCommentRepository
    {
        return new InMemoryCommentRepository([
            '1' => [
                CommentFactory::createMany(5)
            ]
        ]);
    }
}
