<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\EventListener;

use App\Application\Exception\BadRequestException;
use App\Domain\Social\Exception\FailToPublishCommentException;
use App\Infrastructure\Symfony\EventListener\JsonResponseExceptionEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class JsonResponseExceptionEventListenerTest extends TestCase
{
    private JsonResponseExceptionEventListener $listener;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->listener = new JsonResponseExceptionEventListener(debug: true);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener->setLogger($this->logger);
    }

    public function testInvokeHandlesBrowserLikeRequest(): void
    {
        $request = Request::create('/');
        $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $event = $this->event($request, new \Exception('Test error'));

        $this->listener->__invoke($event);

        $this->assertNotNull($event->getResponse());
        $this->assertSame('application/json', $event->getResponse()->headers->get('Content-Type'));
    }

    public function testInvokeHandlesGetRequestWithoutContentType(): void
    {
        // A plain GET has no Content-Type: errors must still be rendered as JSON
        $event = $this->event(Request::create('/articles/1'), new NotFoundHttpException('Not found'));

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('Not found', json_decode($response->getContent(), true)['error']);
    }

    public function testInvokeHandlesRequestAcceptingJson(): void
    {
        $request = Request::create('/');
        $request->headers->set('Accept', 'application/json');
        $event = $this->event($request, new \Exception('Test error'));

        $this->listener->__invoke($event);

        $this->assertNotNull($event->getResponse());
    }

    public function testInvokeHandlesBadRequestException(): void
    {
        $event = $this->event($this->jsonRequest(), BadRequestException::create('Invalid input'));

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
        $event = $this->event($this->jsonRequest(), new NotFoundHttpException('Not found'));

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testInvokeKeepsHttpExceptionHeaders(): void
    {
        $exception = new HttpException(401, 'Unauthorized', headers: ['WWW-Authenticate' => 'Bearer']);
        $event = $this->event($this->jsonRequest(), $exception);

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Bearer', $response->headers->get('WWW-Authenticate'));
    }

    public function testInvokeConverts5xxTo502(): void
    {
        $exception = new class extends \RuntimeException implements HttpExceptionInterface {
            public function getStatusCode(): int { return 500; }
            public function getHeaders(): array { return []; }
        };
        $event = $this->event($this->jsonRequest(), $exception);

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(502, $response->getStatusCode());
    }

    public function testInvokeConvertsUpstreamHttpClientFailureTo502(): void
    {
        $event = $this->event($this->jsonRequest(), new TransportException('Connection timed out'));

        $this->listener->__invoke($event);

        $this->assertSame(502, $event->getResponse()->getStatusCode());
    }

    public function testInvokeConvertsFailToPublishCommentTo502(): void
    {
        $event = $this->event($this->jsonRequest(), new FailToPublishCommentException('Strapi is down'));

        $this->listener->__invoke($event);

        $this->assertSame(502, $event->getResponse()->getStatusCode());
    }

    public function testInvokeIncludesDebugInfoWhenDebugEnabled(): void
    {
        $event = $this->event($this->jsonRequest(), new \Exception('Test error', 123));

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Test error', $data['error']);
        $this->assertArrayHasKey('debug', $data);
        $this->assertArrayHasKey('class', $data['debug']);
        $this->assertArrayHasKey('code', $data['debug']);
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('line', $data['debug']);
    }

    public function testInvokeHidesDetailsWhenDebugDisabled(): void
    {
        $listener = new JsonResponseExceptionEventListener(debug: false);
        $event = $this->event($this->jsonRequest(), new \Exception('Sensitive internal error'));

        $listener->__invoke($event);

        $data = json_decode($event->getResponse()->getContent(), true);
        $this->assertSame('An error occurred', $data['error']);
        $this->assertArrayNotHasKey('debug', $data);
    }

    public function testInvokeUnwrapsHandlerFailedException(): void
    {
        $originalException = new \Exception('Original error');
        $envelope = new Envelope(new \stdClass());
        $handlerException = new HandlerFailedException($envelope, [$originalException]);
        $event = $this->event($this->jsonRequest(), $handlerException);

        $this->listener->__invoke($event);

        $response = $event->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Original error', $data['error']);
    }

    public function testInvokeWorksWithoutLogger(): void
    {
        $listener = new JsonResponseExceptionEventListener(debug: true);
        $event = $this->event($this->jsonRequest(), BadRequestException::create('Error'));

        $listener->__invoke($event);

        $this->assertNotNull($event->getResponse());
    }

    private function jsonRequest(): Request
    {
        $request = Request::create('/');
        $request->headers->set('Content-Type', 'application/json');

        return $request;
    }

    private function event(Request $request, \Throwable $throwable): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }
}
