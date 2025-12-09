<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Github\Model\Factory;

use App\Domain\Coding\Project;
use App\Infrastructure\External\Github\Model\Factory\ProjectFactory;
use App\Infrastructure\External\Github\Model\GithubRepository;
use PHPUnit\Framework\TestCase;

final class ProjectFactoryTest extends TestCase
{
    private ProjectFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ProjectFactory();
    }

    public function testFromGithubRepositoryCreatesProject(): void
    {
        $githubRepo = $this->createGithubRepository('my-project', 'https://github.com/user/my-project', 'PHP', 'A great project');

        $project = $this->factory->fromGithubRepository($githubRepo);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame('my-project', $project->name);
        $this->assertSame('https://github.com/user/my-project', $project->url);
        $this->assertSame('PHP', $project->language);
        $this->assertSame('A great project', $project->description);
    }

    public function testFromGithubRepositoryHandlesNullValues(): void
    {
        $githubRepo = $this->createGithubRepository('project', 'https://github.com/user/project', null, null);

        $project = $this->factory->fromGithubRepository($githubRepo);

        $this->assertNull($project->language);
        $this->assertNull($project->description);
    }

    public function testFromGithubRepositoryCollectionCreatesArrayOfProjects(): void
    {
        $repo1 = $this->createGithubRepository('project-1', 'https://github.com/user/project-1', 'PHP', 'Desc 1');
        $repo2 = $this->createGithubRepository('project-2', 'https://github.com/user/project-2', 'JavaScript', 'Desc 2');
        $collection = [$repo1, $repo2];

        $projects = $this->factory->fromGithubRepositoryCollection($collection);

        $this->assertIsArray($projects);
        $this->assertCount(2, $projects);
        $this->assertInstanceOf(Project::class, $projects[0]);
        $this->assertInstanceOf(Project::class, $projects[1]);
        $this->assertSame('project-1', $projects[0]->name);
        $this->assertSame('project-2', $projects[1]->name);
    }

    public function testFromGithubRepositoryCollectionHandlesEmptyArray(): void
    {
        $projects = $this->factory->fromGithubRepositoryCollection([]);

        $this->assertIsArray($projects);
        $this->assertEmpty($projects);
    }

    private function createGithubRepository(string $name, string $htmlUrl, ?string $language, ?string $description): GithubRepository
    {
        $repo = new GithubRepository();
        $repo->name = $name;
        $repo->htmlUrl = $htmlUrl;
        $repo->language = $language;
        $repo->description = $description;

        return $repo;
    }
}

