<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebProfilerConfig;

return static function (WebProfilerConfig $profiler, ContainerConfigurator $container) {
    if ($container->env() === 'dev') {
        $profiler
            ->toolbar(true)
            ->interceptRedirects(false);
    }

    if ($container->env() === 'test') {
        $profiler
            ->toolbar(false)
            ->interceptRedirects(false);
    }
};
