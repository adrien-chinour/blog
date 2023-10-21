<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $encore, ContainerConfigurator $container): void {
    $encore
        ->outputPath('%kernel.project_dir%/public/build')
        ->scriptAttributes('defer', true)
        ->scriptAttributes('data-turbo-track', 'reload')
        ->linkAttributes('data-turbo-track', 'reload');

    if ($container->env() === 'prod') {
        $encore->cache('true');
    }

    if ($container->env() === 'test') {
        $encore->strictMode(false);
    }
};
