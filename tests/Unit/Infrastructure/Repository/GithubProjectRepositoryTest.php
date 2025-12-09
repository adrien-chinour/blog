<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Coding\Project;
use App\Infrastructure\External\Github\Http\GithubApiClient;
use App\Infrastructure\External\Github\Model\Factory\ProjectFactory;
use App\Infrastructure\External\Github\Model\GithubRepository;
use App\Infrastructure\Repository\GithubProjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GithubProjectRepositoryTest extends TestCase
{
    private GithubApiClient&MockObject $githubApiClient;
    private ProjectFactory&MockObject $projectFactory;
    private GithubProjectRepository $repository;

    protected function setUp(): void
    {
        $this->githubApiClient = $this->createMock(GithubApiClient::class);
        $this->projectFactory = $this->createMock(ProjectFactory::class);
        $this->repository = new GithubProjectRepository($this->githubApiClient, $this->projectFactory);
    }

    public function testLatestReturnsProjects(): void
    {
        $limit = 5;
        $githubRepos = [
            $this->createGithubRepository('repo1', 'https://github.com/user/repo1'),
            $this->createGithubRepository('repo2', 'https://github.com/user/repo2'),
        ];
        $projects = [
            new Project('repo1', 'https://github.com/user/repo1'),
            new Project('repo2', 'https://github.com/user/repo2'),
        ];

        $this->githubApiClient->expects($this->once())
            ->method('getRepositories')
            ->with($limit)
            ->willReturn($githubRepos);

        $this->projectFactory->expects($this->once())
            ->method('fromGithubRepositoryCollection')
            ->with($githubRepos)
            ->willReturn($projects);

        $result = $this->repository->latest($limit);

        $this->assertSame($projects, $result);
    }

    public function testLatestHandlesEmptyResults(): void
    {
        $limit = 10;

        $this->githubApiClient->expects($this->once())
            ->method('getRepositories')
            ->with($limit)
            ->willReturn([]);

        $this->projectFactory->expects($this->once())
            ->method('fromGithubRepositoryCollection')
            ->with([])
            ->willReturn([]);

        $result = $this->repository->latest($limit);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    private function createGithubRepository(string $name, string $htmlUrl): GithubRepository
    {
        $repo = new GithubRepository();
        $repo->name = $name;
        $repo->htmlUrl = $htmlUrl;
        $repo->description = null;
        $repo->language = null;

        return $repo;
    }
}

