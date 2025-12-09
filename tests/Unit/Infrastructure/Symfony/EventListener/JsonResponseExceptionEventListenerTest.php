<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\EventListener;

use App\Application\Exception\BadRequestException;
use App\Infrastructure\Symfony\EventListener\JsonResponseExceptionEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class JsonResponseExceptionEventListenerTest extends TestCase
{
    private JsonResponseExceptionEventListener $listener;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->listener = new JsonResponseExceptionEventListener();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener->setLogger($this->logger);
        $_SERVER['APP_ENV'] = 'test'; // Enable debug mode
    }

    protected function tearDown(): void
    {
        unset($_SERVER['APP_ENV']);
    }

    public function testInvokeSkipsWhenNotJsonRequest(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'text/html');
        $exception = new \Exception('Test error');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->listener->__invoke($event);

        $this->assertNull($event->getResponse());
    }

    public function testInvokeHandlesBadRequestException(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $exception = BadRequestException::create('Invalid input');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->logger->expects($this->once())
            ->method('error');

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Invalid input', $data['error']);
    }

    public function testInvokeHandlesHttpException(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $exception = new NotFoundHttpException('Not found');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testInvokeConverts5xxTo502(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $exception = new class extends \RuntimeException implements \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface {
            public function getStatusCode(): int { return 500; }
            public function getHeaders(): array { return []; }
        };
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(502, $response->getStatusCode());
    }

    public function testInvokeIncludesDebugInfoInTestEnvironment(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $exception = new \Exception('Test error', 123);
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('debug', $data);
        $this->assertArrayHasKey('class', $data['debug']);
        $this->assertArrayHasKey('code', $data['debug']);
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('line', $data['debug']);
    }

    public function testInvokeUnwrapsHandlerFailedException(): void
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $originalException = new \Exception('Original error');
        $envelope = new \Symfony\Component\Messenger\Envelope(new \stdClass());
        $handlerException = new HandlerFailedException($envelope, [$originalException]);
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $handlerException
        );

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Original error', $data['error']);
    }

    public function testInvokeWorksWithoutLogger(): void
    {
        $listener = new JsonResponseExceptionEventListener();
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');
        $exception = BadRequestException::create('Error');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->__invoke($event);

        $this->assertNotNull($event->getResponse());
    }
}

