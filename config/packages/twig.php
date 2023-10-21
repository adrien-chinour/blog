<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig, ContainerConfigurator $container): void {
    $twig->defaultPath('%kernel.project_dir%/templates');

    if (in_array($container->env(), ['dev', 'test'], true)) {
        $twig->strictVariables(true);
    }
};
