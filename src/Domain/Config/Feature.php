<?php

declare(strict_types=1);

namespace App\Domain\Config;

final readonly class Feature
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $enable,
    ) {}
}
