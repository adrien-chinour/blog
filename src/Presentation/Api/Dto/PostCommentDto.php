<?php

declare(strict_types=1);

namespace App\Presentation\Api\Dto;

final readonly class PostCommentDto
{
    public function __construct(
        public string $articleId,
        public string $username,
        public string $message,
    ) {}
}
