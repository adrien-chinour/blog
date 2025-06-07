<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebProfilerConfig;

if (!class_exists('\Symfony\Bundle\WebProfilerBundle\WebProfilerBundle')) {
    return;
}

return static function (WebProfilerConfig $profiler, ContainerConfigurator $container) {
    if ($container->env() === 'dev') {
        $profiler->interceptRedirects(false);
        $profiler->toolbar()->enabled(true);
    }

    if ($container->env() === 'test') {
        $profiler->interceptRedirects(false);
        $profiler->toolbar()->enabled(false);
    }
};
