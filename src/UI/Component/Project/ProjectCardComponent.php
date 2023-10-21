<?php

declare(strict_types=1);

namespace App\UI\Component\Project;

use App\Domain\Coding\Project;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ProjectCard')]
final class ProjectCardComponent
{
    public Project $project;
}
