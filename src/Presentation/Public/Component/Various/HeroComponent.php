<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Various;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Hero')]
class HeroComponent
{
    public ?string $title = null;

    public ?string $description = null;
}
