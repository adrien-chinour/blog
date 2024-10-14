<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Infrastructure\Meilisearch\MeilisearchClientFactory;
use Meilisearch\Client;

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
        ->bind('$adminToken', '%env(ADMIN_TOKEN)%')
        ->bind('$meilisearchHost', '%env(MEILISEARCH_HOST)%')
        ->bind('$meilisearchToken', '%env(MEILISEARCH_TOKEN)%')
        ->bind('$baserowCommentTable', '%env(BASEROW_COMMENT_TABLE)%')
        ->bind('$baserowFeatureTable', '%env(BASEROW_FEATURE_TABLE)%');


    /**
     * Automatically registers App namespace has services
     */
    $services
        ->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    $services->set(Client::class)->factory(service(MeilisearchClientFactory::class));
};
