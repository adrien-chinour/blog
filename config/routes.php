<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('../src/Presentation/Public/', 'attribute');

    $routes->import('../src/Presentation/Admin', 'attribute')
        ->prefix('/admin')
        ->namePrefix('admin_');

    $routes->import('../src/Presentation/Api', 'attribute')
        ->prefix('/api')
        ->namePrefix('api_');

    $routes->import('../src/Presentation/Webhook/', 'attribute')
        ->prefix('/webhook')
        ->namePrefix('webhook_');

    if ($routes->env() === 'dev') {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')->prefix('/_wdt');
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')->prefix('/_profiler');
        $routes->import('@FrameworkBundle/Resources/config/routing/errors.xml', 'attribute')->prefix('/_error');
    }
};
