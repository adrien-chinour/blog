<?php

declare(strict_types=1);

namespace App\Application\Query\GetProjectList;

use App\Application\Query\CacheableQueryInterface;

final readonly class GetProjectListQuery implements CacheableQueryInterface
{
    public function __construct(public int $limit = 10) {}

    public function getCacheKey(): string
    {
        return sprintf('project_list_limit_%s', $this->limit);
    }

    public function getCacheTtl(): int
    {
        return 3600;
    }
}
