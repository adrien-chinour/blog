<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class StopwatchMiddleware implements MiddlewareInterface
{
    public function __construct(private Stopwatch $stopwatch) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->stopwatch->start($name = $envelope->getMessage()::class, 'messenger.message');

        $envelope = $stack->next()->handle($envelope, $stack);

        $this->stopwatch->stop($name);

        return $envelope;
    }
}
