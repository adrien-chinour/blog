<?php

declare(strict_types=1);

namespace App\Presentation\Component\Project;

use App\Domain\Coding\Project;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ProjectCard')]
final class ProjectCardComponent
{
    public Project $project;
}
