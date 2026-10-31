<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('../src/Presentation/Http/**/*', 'attribute');

    if (in_array($routes->env(), ['dev', 'test'], true)) {
        // OpenAPI documentation (NelmioApiDocBundle, dev dependency)
        $routes->add('app.swagger_ui', '/api/doc')
            ->controller('nelmio_api_doc.controller.swagger_ui')
            ->methods(['GET']);
        $routes->add('app.swagger', '/api/doc.json')
            ->controller('nelmio_api_doc.controller.swagger')
            ->methods(['GET']);
    }

    if ($routes->env() === 'dev') {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')->prefix('/_wdt');
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')->prefix('/_profiler');
        $routes->import('@FrameworkBundle/Resources/config/routing/errors.xml', 'attribute')->prefix('/_error');
    }
};
