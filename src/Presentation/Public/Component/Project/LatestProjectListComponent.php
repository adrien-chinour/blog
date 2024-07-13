<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Project;

use App\Application\Query\GetProjectList\GetProjectListQuery;
use App\Domain\Coding\Project;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Webmozart\Assert\Assert;

#[AsTwigComponent('LatestProjectList')]
final class LatestProjectListComponent
{
    use HandleTrait;

    public string $title = 'Les derniers projets.';

    public int $limit = 3;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return Project[]
     */
    public function projects(): array
    {
        $projects = $this->handle(new GetProjectListQuery($this->limit));

        Assert::isArray($projects);
        Assert::allIsInstanceOf($projects, Project::class);

        return $projects;
    }
}
