<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\Messenger\Middleware;

use stdClass;
use App\Infrastructure\Symfony\Messenger\Middleware\LoggerMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class LoggerMiddlewareTest extends TestCase
{
    private LoggerInterface&MockObject $logger;
    private LoggerMiddleware $middleware;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->middleware = new LoggerMiddleware();
        $this->middleware->setLogger($this->logger);
    }

    public function testHandleLogsMessageStartAndEnd(): void
    {
        $message = new stdClass();
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);

        $calls = [];
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(static function (string $message, array $context) use (&$calls): void {
                $calls[] = [$message, $context];
            });

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
        $this->assertSame('Handling message {message}, started at {timestamp}', $calls[0][0]);
        $this->assertArrayHasKey('message', $calls[0][1]);
        $this->assertArrayHasKey('timestamp', $calls[0][1]);
        $this->assertSame('Message {message} handled in {time}ms', $calls[1][0]);
        $this->assertArrayHasKey('message', $calls[1][1]);
        $this->assertArrayHasKey('time', $calls[1][1]);
    }

    public function testHandleWorksWithoutLogger(): void
    {
        $this->logger->expects($this->never())->method($this->anything());
        $middleware = new LoggerMiddleware();
        $message = new stdClass();
        $envelope = new Envelope($message);
        $stack = $this->createMock(StackInterface::class);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $stack->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $result = $middleware->handle($envelope, $stack);

        $this->assertSame($envelope, $result);
    }

    public function testMicrotimeReturnsMilliseconds(): void
    {
        $this->logger->expects($this->never())->method($this->anything());
        $time = $this->middleware->microtime();

        $this->assertIsFloat($time);
        $this->assertGreaterThan(0, $time);
        // Should be in milliseconds (large number)
        $this->assertGreaterThan(1000000000, $time);
    }
}

