<?php

declare(strict_types=1);

namespace App\Application\Query\GetProjectList;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 3600, tags: ['list_project', 'project'])]
final readonly class GetProjectListQuery
{
    public function __construct(
        public int $limit = 10,
    ) {}
}
