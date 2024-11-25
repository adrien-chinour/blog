<?php

declare(strict_types=1);

namespace App\Domain\Layout;

final readonly class Page
{
    public function __construct(
        public string $title,
        public string $path,
        public string $content,
    ) {}
}
