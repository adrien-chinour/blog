<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $container->parameters()
        ->set('router.request_context.scheme', 'https')
        ->set('asset.request_context.secure', true);

    /**
     * Sets default configuration for services
     */
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$githubUser', '%env(GITHUB_USER)%')
        ->bind('$contentfulSpaceId', '%env(CONTENTFUL_SPACE_ID)%')
        ->bind('$adminToken', '%env(ADMIN_TOKEN)%');

    /**
     * Automatically registers App namespace has services
     */
    $services
        ->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');
};
