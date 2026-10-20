<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Messenger\Middleware;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class LoggerMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $startedAt = $this->microtime();

        $this->logger?->info('Handling message {message}, started at {timestamp}', [
            'message' => $envelope->getMessage()::class,
            'timestamp' => $startedAt,
        ]);

        $envelope = $stack->next()->handle($envelope, $stack);

        $this->logger?->info('Message {message} handled in {time}ms', [
            'message' => $envelope->getMessage()::class,
            'time' => $this->microtime() - $startedAt,
        ]);

        return $envelope;
    }

    public function microtime(): float
    {
        return floor(microtime(true) * 1000);
    }
}
