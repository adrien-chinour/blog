<?php

declare(strict_types=1);

namespace App\Domain\Coding;

interface ProjectRepository
{
    /**
     * @return Project[]
     */
    public function latest(int $limit): array;
}
