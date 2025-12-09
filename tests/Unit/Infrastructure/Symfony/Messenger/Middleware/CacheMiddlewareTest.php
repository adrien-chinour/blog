<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\Messenger\Middleware;

use App\Application\Query\QueryCache;
use App\Infrastructure\Component\Cache\QueryCacheConfig;
use App\Infrastructure\Component\Cache\QueryCacheResolver;
use App\Infrastructure\Symfony\Messenger\Middleware\CacheMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class CacheMiddlewareTest extends TestCase
{
    private TagAwareCacheInterface&MockObject $cache;
    private QueryCacheResolver&MockObject $resolver;
    private CacheMiddleware $middleware;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(TagAwareCacheInterface::class);
        $this->resolver = $this->createMock(QueryCacheResolver::class);
        $this->middleware = new CacheMiddleware($this->cache, $this->resolver);
    }

    public function testHandleSkipsCacheWhenNoConfig(): void
    {
        $message = new class {};
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with($message)
            ->willReturn(null);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $stack->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertSame($envelope, $result);
    }

    public function testHandleCachesResultWhenConfigPresent(): void
    {
        $message = new #[QueryCache(ttl: 3600, tags: ['article'])] class {
            public string $slug = 'test-slug';
        };
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);
        $config = new QueryCacheConfig('cache-key', 3600, ['article']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with($message)
            ->willReturn($config);

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(false);
        $cacheItem->expects($this->once())
            ->method('set')
            ->with($this->isInstanceOf(Envelope::class))
            ->willReturnSelf();
        $cacheItem->expects($this->once())
            ->method('expiresAfter')
            ->with(3600)
            ->willReturnSelf();
        $cacheItem->expects($this->once())
            ->method('tag')
            ->with(['article'])
            ->willReturnSelf();
        $cacheItem->expects($this->once())
            ->method('getKey')
            ->willReturn('cache-key');
        $cacheItem->expects($this->once())
            ->method('get')
            ->willReturn($envelope);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $stack->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $this->cache->expects($this->once())
            ->method('get')
            ->with('cache-key', $this->isType('callable'))
            ->willReturnCallback(function ($key, $callback) use ($cacheItem) {
                return $callback($cacheItem);
            });

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertInstanceOf(Envelope::class, $result);
    }

    public function testHandleReturnsCachedResultWhenHit(): void
    {
        $message = new #[QueryCache(ttl: 3600, tags: ['article'])] class {};
        $envelope = new Envelope($message);
        $cachedEnvelope = new Envelope(new \stdClass());
        $stack = $this->createMock(StackInterface::class);
        $config = new QueryCacheConfig('cache-key', 3600, ['article']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->willReturn($config);

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        $cacheItem->expects($this->once())
            ->method('get')
            ->willReturn($cachedEnvelope);

        $this->cache->expects($this->once())
            ->method('get')
            ->with('cache-key', $this->isType('callable'))
            ->willReturnCallback(function ($key, $callback) use ($cacheItem) {
                return $callback($cacheItem);
            });

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertSame($cachedEnvelope, $result);
    }
}

