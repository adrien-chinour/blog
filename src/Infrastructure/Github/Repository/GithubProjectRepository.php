<?php

declare(strict_types=1);

namespace App\Infrastructure\Github\Repository;

use App\Domain\Coding\ProjectRepository;
use App\Infrastructure\Github\Http\GithubApiClient;
use App\Infrastructure\Github\Model\Factory\ProjectFactory;

final readonly class GithubProjectRepository implements ProjectRepository
{
    public function __construct(private GithubApiClient $githubApiClient, private ProjectFactory $projectFactory) {}

    public function latest(int $limit): array
    {
        return $this->projectFactory->fromGithubRepositoryCollection($this->githubApiClient->getRepositories($limit));
    }
}
