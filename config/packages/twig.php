<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig, ContainerConfigurator $container): void {
    $twig->defaultPath('%kernel.project_dir%/templates');

    $twig->global('site')->value([
        'name' => 'Undefined',
        'description' => 'Un blog centré sur le développement web en PHP. Retrouvez des articles sur mes trouvailles et expérimentations.',
        'url' => 'https://www.udfn.fr',
    ]);

    if (in_array($container->env(), ['dev', 'test'], true)) {
        $twig->strictVariables(true);
    }
};
