<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('../src/Presentation/Http/**/*', 'attribute');
    $routes->import('@FrameworkBundle/Resources/config/routing/webhook.xml')->prefix('/webhook');

    if ($routes->env() === 'dev') {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')->prefix('/_wdt');
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')->prefix('/_profiler');
        $routes->import('@FrameworkBundle/Resources/config/routing/errors.xml', 'attribute')->prefix('/_error');
    }
};
