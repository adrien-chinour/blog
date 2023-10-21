<?php

declare(strict_types=1);

namespace App\Domain\Coding;

final readonly class Project
{
    public function __construct(
        public string $name,
        public string $url,
        public ?string $language = null,
        public ?string $description = null,
    ) {}
}
