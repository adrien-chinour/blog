<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Coding\Project;
use App\Domain\Coding\ProjectRepository;

final readonly class InMemoryProjectRepository implements ProjectRepository
{
    public function __construct(
        /** @var Project[] */
        private array $projects = [],
    ) {}

    public function latest(int $limit): array
    {
        return array_slice($this->projects, 0, $limit);
    }
}
