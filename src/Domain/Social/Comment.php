<?php

declare(strict_types=1);

namespace App\Domain\Social;

use DateTimeImmutable;

final readonly class Comment
{
    public function __construct(
        public string $id,
        public string $username,
        public string $message,
        public DateTimeImmutable $publishedAt
    ) {}
}
