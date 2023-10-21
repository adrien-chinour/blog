<?php

declare(strict_types=1);

namespace App\Application\Query;

interface CacheableQueryInterface
{
    public function getCacheKey(): string;

    public function getCacheTtl(): int;
}
