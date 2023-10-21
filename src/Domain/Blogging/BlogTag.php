<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

final readonly class BlogTag
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
    ) {}
}
