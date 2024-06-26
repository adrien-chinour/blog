<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Various;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Header')]
final class HeaderComponent
{
    public string $title = 'Undefined';

    public function navigationItems(): array
    {
        return [
            ['route' => 'article_list', 'label' => 'Articles'],
            ['route' => 'project_list', 'label' => 'Projets'],
        ];
    }
}
