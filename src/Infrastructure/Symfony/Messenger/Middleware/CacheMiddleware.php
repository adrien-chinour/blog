<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Messenger\Middleware;

use App\Infrastructure\Cache\QueryCacheConfig;
use App\Infrastructure\Cache\QueryCacheResolver;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Webmozart\Assert\Assert;

/**
 * Inspired by https://thomas.jarrand.fr/blog/cache-query-avec-symfony-messenger/
 */
final class CacheMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly TagAwareCacheInterface $messengerCache,
        private readonly QueryCacheResolver $resolver,
    ) {}

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $config = $this->resolver->resolve($message);

        if (!($config instanceof QueryCacheConfig)) {
            $this->logger?->info(
                'Message of {message_class} is not cacheable, skipping',
                ['message_class' => $message::class]
            );

            return $this->continue($envelope, $stack);
        }

        return $this->messengerCache->get($config->key, function (ItemInterface $item) use ($envelope, $stack, $config): Envelope {
            $this->logger?->info(
                'Cache {key} result in hit={hit}',
                ['key' => $item->getKey(), 'hit' => $item->isHit()]
            );

            if (!$item->isHit()) {
                $item
                    ->set($this->continue($envelope, $stack))
                    ->expiresAfter($config->ttl)
                    ->tag($config->tags);
            }

            Assert::isInstanceOf($value = $item->get(), Envelope::class);

            return $value;
        });
    }

    private function continue(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}
