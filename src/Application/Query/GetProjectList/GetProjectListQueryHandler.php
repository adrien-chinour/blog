<?php

declare(strict_types=1);

namespace App\Application\Query\GetProjectList;

use App\Domain\Coding\Project;
use App\Domain\Coding\ProjectRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProjectListQueryHandler
{
    public function __construct(private ProjectRepository $projectRepository) {}

    /**
     * @param GetProjectListQuery $query
     * @return Project[]
     */
    public function __invoke(GetProjectListQuery $query): array
    {
        return $this->projectRepository->latest($query->limit);
    }
}
