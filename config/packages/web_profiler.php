<?php

use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebProfilerConfig;

if (!class_exists(WebProfilerBundle::class)) {
    return;
}

return static function (WebProfilerConfig $profiler, ContainerConfigurator $container): void {
    if ($container->env() === 'dev') {
        $profiler->interceptRedirects(false);
        $profiler->toolbar()->enabled(true);
    }

    if ($container->env() === 'test') {
        $profiler->interceptRedirects(false);
        $profiler->toolbar()->enabled(false);
    }
};
