<?php

declare(strict_types=1);

namespace App\Application\Command\TagCacheInvalidation;

final readonly class TagCacheInvalidationCommand
{
    public function __construct(
        public array $tags
    ) {}
}
