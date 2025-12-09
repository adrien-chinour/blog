<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Blogging\BlogArticleSearchRepository;
use App\Domain\Coding\ProjectRepository;
use App\Domain\Config\FeatureRepository;
use App\Domain\Layout\PageRepository;
use App\Domain\Social\CommentRepository;
use App\Infrastructure\External\Meilisearch\MeilisearchClientFactory;
use App\Infrastructure\Repository\ContentfulBlogArticleRepository;
use App\Infrastructure\Repository\GithubProjectRepository;
use App\Infrastructure\Repository\InMemoryBlogArticleRepository;
use App\Infrastructure\Repository\InMemoryBlogArticleSearchRepository;
use App\Infrastructure\Repository\InMemoryCommentRepository;
use App\Infrastructure\Repository\InMemoryFeatureRepository;
use App\Infrastructure\Repository\InMemoryPageRepository;
use App\Infrastructure\Repository\InMemoryProjectRepository;
use App\Infrastructure\Repository\MeilisearchBlogArticleSearchRepository;
use App\Infrastructure\Repository\StrapiCommentRepository;
use App\Infrastructure\Repository\StrapiFeatureRepository;
use App\Infrastructure\Repository\StrapiPageRepository;
use App\Infrastructure\Symfony\EventListener\RateLimiterEventListener;
use App\Tests\Factory\Repository\InMemoryBlogArticleRepositoryFactory;
use App\Tests\Factory\Repository\InMemoryCommentRepositoryFactory;
use App\Tests\Factory\Repository\InMemoryFeatureRepositoryFactory;
use App\Tests\Factory\Repository\InMemoryPageRepositoryFactory;
use App\Tests\Factory\Repository\InMemoryProjectRepositoryFactory;
use Meilisearch\Client as MeilisearchClient;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

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
        ->bind('$meilisearchToken', '%env(MEILISEARCH_TOKEN)%');

    /**
     * Automatically registers App namespace has services
     */
    $services->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}')
        ->exclude('../src/Infrastructure/Component/OpenTelemetry/*');

    /**
     * Service factories
     */
    $services->set(MeilisearchClient::class)->factory(service(MeilisearchClientFactory::class));

    /**
     * Repository implementations used on dev/prod environnements
     * Override in test env with InMemory repositories
     */
    $services->alias(BlogArticleRepository::class, ContentfulBlogArticleRepository::class);
    $services->alias(BlogArticleSearchRepository::class, MeilisearchBlogArticleSearchRepository::class);
    $services->alias(ProjectRepository::class, GithubProjectRepository::class);
    $services->alias(FeatureRepository::class, StrapiFeatureRepository::class);
    $services->alias(CommentRepository::class, StrapiCommentRepository::class);
    $services->alias(PageRepository::class, StrapiPageRepository::class);

    /**
     * Override repositories on test environnement with InMemory implementation
     */
    if ($container->env() === 'test') {
        $services->alias(BlogArticleRepository::class, InMemoryBlogArticleRepository::class);
        $services->set(InMemoryBlogArticleRepository::class)->factory([InMemoryBlogArticleRepositoryFactory::class, 'create']);

        $services->alias(BlogArticleSearchRepository::class, InMemoryBlogArticleSearchRepository::class);
        $services->set(InMemoryBlogArticleSearchRepository::class)->factory([InMemoryBlogArticleRepositoryFactory::class, 'createSearch']);

        $services->alias(ProjectRepository::class, InMemoryProjectRepository::class);
        $services->set(InMemoryProjectRepository::class)->factory([InMemoryProjectRepositoryFactory::class, 'create']);

        $services->alias(FeatureRepository::class, InMemoryFeatureRepository::class);
        $services->set(InMemoryFeatureRepository::class)->factory([InMemoryFeatureRepositoryFactory::class, 'create']);

        $services->alias(CommentRepository::class, InMemoryCommentRepository::class);
        $services->set(InMemoryCommentRepository::class)->factory([InMemoryCommentRepositoryFactory::class, 'create']);

        $services->alias(PageRepository::class, InMemoryPageRepository::class);
        $services->set(InMemoryPageRepository::class)->factory([InMemoryPageRepositoryFactory::class, 'create']);
    }

    /**
     * Register rate limiter event listener only in non-test environments
     */
    if ($container->env() !== 'test') {
        $services->set(RateLimiterEventListener::class)
            ->tag('kernel.event_listener', ['event' => RequestEvent::class, 'method' => 'onRequest'])
            ->tag('kernel.event_listener', ['event' => ResponseEvent::class, 'method' => 'onResponse']);
    }
};
