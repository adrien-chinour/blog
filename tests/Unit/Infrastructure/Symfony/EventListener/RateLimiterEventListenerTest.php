<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\EventListener;

use App\Infrastructure\Symfony\EventListener\RateLimiterEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class RateLimiterEventListenerTest extends TestCase
{
    private RateLimiterFactoryInterface&MockObject $limiterFactory;
    private AuthorizationCheckerInterface&MockObject $checker;
    private RateLimiterEventListener $listener;

    /** @var string[] */
    private array $previousTrustedProxies;
    private int $previousTrustedHeaderSet;

    protected function setUp(): void
    {
        $this->limiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $this->checker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->listener = new RateLimiterEventListener($this->limiterFactory, $this->checker);

        $this->previousTrustedProxies = Request::getTrustedProxies();
        $this->previousTrustedHeaderSet = Request::getTrustedHeaderSet();
    }

    protected function tearDown(): void
    {
        Request::setTrustedProxies($this->previousTrustedProxies, $this->previousTrustedHeaderSet);
    }

    public function testOnRequestSkipsWhenAdmin(): void
    {
        $request = Request::create('/');
        $event = new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->checker->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $this->limiterFactory->expects($this->never())
            ->method('create');

        $this->listener->onRequest($event);

        $this->assertNull($event->getResponse());
    }

    public function testOnRequestSkipsWhenNotMainRequest(): void
    {
        $request = Request::create('/');
        $event = new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->limiterFactory->expects($this->never())
            ->method('create');

        $this->listener->onRequest($event);
    }

    public function testOnRequestSets429WhenRateLimitExceeded(): void
    {
        $request = Request::create('/');
        $event = new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->checker->expects($this->once())
            ->method('isGranted')
            ->willReturn(false);

        $limiter = $this->createMock(LimiterInterface::class);
        $rateLimit = $this->createMock(RateLimit::class);
        $rateLimit->expects($this->once())
            ->method('isAccepted')
            ->willReturn(false);

        $limiter->expects($this->once())
            ->method('consume')
            ->willReturn($rateLimit);

        $this->limiterFactory->expects($this->once())
            ->method('create')
            ->willReturn($limiter);

        $this->listener->onRequest($event);

        $this->assertNotNull($event->getResponse());
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $event->getResponse()->getStatusCode());
    }

    public function testOnResponseSkipsWhenAdmin(): void
    {
        $response = new Response('', Response::HTTP_NOT_FOUND);
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->checker->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $this->limiterFactory->expects($this->never())
            ->method('create');

        $this->listener->onResponse($event);

        $this->assertFalse($response->headers->has('X-RateLimit-Remaining'));
    }

    public function testOnResponseSkipsWhenNotMainRequest(): void
    {
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            HttpKernelInterface::SUB_REQUEST,
            new Response('', Response::HTTP_NOT_FOUND)
        );

        $this->limiterFactory->expects($this->never())
            ->method('create');

        $this->listener->onResponse($event);
    }

    public function testOnResponseAddsRateLimitHeaders(): void
    {
        $request = Request::create('/');
        $response = new Response();
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $limiter = $this->createMock(LimiterInterface::class);
        $rateLimit = $this->createMock(RateLimit::class);
        $rateLimit->expects($this->once())
            ->method('getRemainingTokens')
            ->willReturn(950);
        $rateLimit->expects($this->once())
            ->method('getLimit')
            ->willReturn(1000);

        $limiter->expects($this->once())
            ->method('consume')
            ->with(0)
            ->willReturn($rateLimit);

        $this->limiterFactory->expects($this->once())
            ->method('create')
            ->willReturn($limiter);

        $this->listener->onResponse($event);

        $this->assertSame('950', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertSame('1000', $response->headers->get('X-RateLimit-Limit'));
    }

    public function testOnResponseConsumesTokensBasedOnStatusCode(): void
    {
        $request = Request::create('/');
        $response = new Response('', Response::HTTP_NOT_FOUND);
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $limiter = $this->createMock(LimiterInterface::class);
        $rateLimit = $this->createMock(RateLimit::class);
        $rateLimit->method('getRemainingTokens')->willReturn(995);
        $rateLimit->method('getLimit')->willReturn(1000);

        $limiter->expects($this->once())
            ->method('consume')
            ->with(5) // 404 consumes 5 tokens
            ->willReturn($rateLimit);

        $this->limiterFactory->expects($this->once())
            ->method('create')
            ->willReturn($limiter);

        $this->listener->onResponse($event);
    }

    public function testCloudflareIpHeaderIsIgnoredWhenRequestDoesNotComeFromTrustedProxy(): void
    {
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);

        $request = Request::create('/', server: ['REMOTE_ADDR' => '10.0.0.1']);
        $request->headers->set('Cf-Connecting-Ip', '1.2.3.4');

        $this->limiterFactory->expects($this->once())
            ->method('create')
            ->with('10.0.0.1')
            ->willReturn($this->acceptingLimiter());

        $this->listener->onResponse($this->responseEvent($request));
    }

    public function testCloudflareIpHeaderIsUsedWhenRequestComesFromTrustedProxy(): void
    {
        Request::setTrustedProxies(['10.0.0.1'], Request::HEADER_X_FORWARDED_FOR);

        $request = Request::create('/', server: ['REMOTE_ADDR' => '10.0.0.1']);
        $request->headers->set('Cf-Connecting-Ip', '1.2.3.4');

        $this->limiterFactory->expects($this->once())
            ->method('create')
            ->with('1.2.3.4')
            ->willReturn($this->acceptingLimiter());

        $this->listener->onResponse($this->responseEvent($request));
    }

    private function responseEvent(Request $request): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response()
        );
    }

    private function acceptingLimiter(): LimiterInterface&MockObject
    {
        $rateLimit = $this->createMock(RateLimit::class);
        $rateLimit->method('getRemainingTokens')->willReturn(1000);
        $rateLimit->method('getLimit')->willReturn(1000);

        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn($rateLimit);

        return $limiter;
    }
}
