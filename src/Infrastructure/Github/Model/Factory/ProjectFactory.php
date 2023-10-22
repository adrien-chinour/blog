<?php

declare(strict_types=1);

namespace App\Infrastructure\Github\Model\Factory;

use App\Domain\Coding\Project;
use App\Infrastructure\Github\Model\GithubRepository;

final readonly class ProjectFactory
{
    public function fromGithubRepository(GithubRepository $project): Project
    {
        return new Project(
            name: $project->name,
            url: $project->htmlUrl,
            language: $project->language,
            description: $project->description,
        );
    }

    /**
     * @param GithubRepository[] $collection
     * @return Project[]
     */
    public function fromGithubRepositoryCollection(array $collection): array
    {
        return array_map(fn (GithubRepository $project) => $this->fromGithubRepository($project), $collection);
    }
}
