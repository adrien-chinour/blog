<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Project;

use App\Application\Query\GetProjectList\GetProjectListQuery;
use App\Domain\Coding\Project;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ProjectList')]
final class ProjectListComponent
{
    use HandleTrait;

    public int $size = 12;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return Project[]
     */
    public function projects(): array
    {
        return $this->handle(new GetProjectListQuery($this->size));
    }
}
