<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\Messenger\Middleware;

use App\Infrastructure\Symfony\Messenger\Middleware\StopwatchMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchMiddlewareTest extends TestCase
{
    private Stopwatch&MockObject $stopwatch;
    private StopwatchMiddleware $middleware;

    protected function setUp(): void
    {
        $this->stopwatch = $this->createMock(Stopwatch::class);
        $this->middleware = new StopwatchMiddleware($this->stopwatch);
    }

    public function testHandleStartsAndStopsStopwatch(): void
    {
        $message = new \stdClass();
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);

        $this->stopwatch->expects($this->once())
            ->method('start')
            ->with(\stdClass::class, 'messenger.message');

        $this->stopwatch->expects($this->once())
            ->method('stop')
            ->with(\stdClass::class);

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

    public function testHandleUsesMessageClassNameAsEventName(): void
    {
        $message = new class {};
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);

        $messageClass = $message::class;

        $this->stopwatch->expects($this->once())
            ->method('start')
            ->with($messageClass, 'messenger.message');

        $this->stopwatch->expects($this->once())
            ->method('stop')
            ->with($messageClass);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $stack->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $this->middleware->handle($envelope, $stack);
    }
}

