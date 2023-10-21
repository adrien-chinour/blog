<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Messenger\Middleware;

use App\Application\Query\CacheableQueryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Inspired by https://thomas.jarrand.fr/blog/cache-query-avec-symfony-messenger/
 */
final class CacheMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly AdapterInterface $cache) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if (!$message instanceof CacheableQueryInterface) {
            $this->logger?->info(
                'Message of {message_class} is not cacheable, skipping',
                ['message_class' => $message::class]
            );

            return $this->continue($envelope, $stack);
        }

        $item = $this->cache->getItem($message->getCacheKey());

        $this->logger?->info(
            'Cache {key} result in hit={hit}',
            ['key' => $item->getKey(), 'hit' => $item->isHit()]
        );

        if (!$item->isHit()) {
            $item->set($this->continue($envelope, $stack));
            $item->expiresAfter($message->getCacheTtl());
            $this->cache->save($item);
        }

        return $item->get();
    }

    private function continue(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}
