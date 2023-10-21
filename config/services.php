<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    /**
     * Sets default configuration for services
     */
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$githubUser', '%env(GITHUB_USER)%');

    /**
     * Automatically registers App namespace has services
     */
    $services
        ->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');
};
