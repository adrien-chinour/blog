<?php

declare(strict_types=1);

namespace App\UI\Component\Various;


use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Footer')]
final class FooterComponent
{
    public function copyright(): string
    {
        return '© Adrien Chinour';
    }

    public function links(): array
    {
        return [
            [
                'label' => 'Github',
                'url' => 'https://github.com/adrien-chinour/',
                'external' => true
            ],
            [
                'label' => 'Stack Overflow',
                'url' => 'https://stackoverflow.com/users/13884867/adrien-chinour',
                'external' => true
            ],
        ];
    }
}
