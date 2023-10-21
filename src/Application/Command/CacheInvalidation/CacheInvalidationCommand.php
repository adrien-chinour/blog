<?php

declare(strict_types=1);

namespace App\Application\Command\CacheInvalidation;

final readonly class CacheInvalidationCommand
{
    public function __construct(public string $cacheKey) {}
}
