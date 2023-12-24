<?php

use App\Infrastructure\Symfony\Messenger\Middleware\CacheMiddleware;
use App\Infrastructure\Symfony\Messenger\Middleware\LoggerMiddleware;
use App\Infrastructure\Symfony\Messenger\Middleware\StopwatchMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

const APPLICATION_JSON = 'application/json';

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    /**
     * Framework Configuration
     * @see FrameworkConfig
     */
    $framework
        ->secret('%env(APP_SECRET)%')
        ->httpMethodOverride(false)
        ->handleAllThrowables(true)
        ->test($container->env() === 'test')
        ->phpErrors([
            'log' => true,
        ]);

    $framework->propertyInfo()
        ->enabled(true);

    $framework->serializer()
        ->enabled(true)
        ->enableAttributes(true);

    /**
     * Assets Configuration
     * @see \Symfony\Config\Framework\AssetsConfig
     */
    $framework->assets([
        'json_manifest_path' => '%kernel.project_dir%/public/build/manifest.json',
    ]);

    /**
     * Cache Configuration
     * @see \Symfony\Config\Framework\CacheConfig
     */
    $framework->cache()
        ->app('cache.adapter.filesystem')
        ->system('cache.adapter.system');

    /**
     * Router Configuration
     * @see \Symfony\Config\Framework\RouterConfig
     */
    $framework->router()
        ->utf8(true)
        ->strictRequirements($container->env() === 'dev');

    /**
     * Session Configuration
     * @see \Symfony\Config\Framework\SessionConfig
     */
    $framework->session()
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite('lax')
        ->storageFactoryId('session.storage.factory.native');

    if ($container->env() === 'test') {
        $framework->session()
            ->storageFactoryId('session.storage.factory.mock_file');
    }

    /**
     * HttpClient Configuration
     * @see \Symfony\Config\Framework\HttpClientConfig
     */
    $framework->httpClient()
        ->defaultOptions([
            'max_redirects' => 0,
            'timeout' => 3,
        ]);

    $framework->httpClient()
        ->scopedClient('contentful.client', [
            'base_uri' => 'https://graphql.contentful.com',
            'headers' => [
                'Content-Type' => APPLICATION_JSON,
                'Accept' => APPLICATION_JSON,
                'Authorization' => 'Bearer %env(CONTENTFUL_ACCESS_TOKEN)%',
            ],
        ]);

    $framework->httpClient()
        ->scopedClient('github.client', [
            'base_uri' => 'https://api.github.com',
            'headers' => [
                'Content-Type' => APPLICATION_JSON,
                'Accept' => APPLICATION_JSON,
                'Authorization' => 'Bearer %env(GITHUB_ACCESS_TOKEN)%',
            ],
        ]);

    /**
     * Profiler Configuration
     * @see \Symfony\Config\Framework\ProfilerConfig
     */
    if ($container->env() === 'dev') {
        $framework->profiler()
            ->onlyExceptions(false)
            ->onlyMainRequests(false);
    }

    if ($container->env() === 'test') {
        $framework->profiler()
            ->collect(false);
    }

    /**
     * Messenger Configuration
     * @see \Symfony\Config\Framework\MessengerConfig
     */
    $framework->messenger()
        ->transport('sync', 'sync://');

    $framework->messenger()
        ->bus('messenger.bus.default', [
            'middleware' => array_filter([
                $container->env() === 'dev' ? StopwatchMiddleware::class : null,
                LoggerMiddleware::class,
                CacheMiddleware::class,
            ]),
        ]);

    /**
     * RateLimiter Configuration
     * @see \Symfony\Config\Framework\RateLimiterConfig
     */
    $framework->rateLimiter()
        ->limiter('public')
        ->policy('sliding_window')
        ->limit(1000)
        ->interval('60 minutes')
    ;

    /**
     * Lock Configuration
     * @see \Symfony\Config\Framework\LockConfig
     */
    $framework->lock('flock');
};
