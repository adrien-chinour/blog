<?php

namespace App\Infrastructure\Symfony\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsEventListener(RequestEvent::class, 'onRequest')]
#[AsEventListener(ResponseEvent::class, 'onResponse')]
final readonly class RateLimiterEventListener
{
    public function __construct(
        private RateLimiterFactory            $publicLimiter,
        private AuthorizationCheckerInterface $checker
    ) {}

    public function onRequest(RequestEvent $event): void
    {
        if ($this->checker->isGranted('ROLE_ADMIN') || false === $event->isMainRequest()) {
            return;
        }

        $limiter = $this->publicLimiter->create($event->getRequest()->getClientIp());
        if (false === $limiter->consume()->isAccepted()) {
            $event->setResponse(new Response(status: Response::HTTP_TOO_MANY_REQUESTS));
        }
    }

    public function onResponse(ResponseEvent $event): void
    {
        $limiter = $this->publicLimiter->create($event->getRequest()->getClientIp());

        $limit = $limiter->consume(match ($event->getResponse()->getStatusCode()) {
            Response::HTTP_NOT_FOUND => 5,
            Response::HTTP_FORBIDDEN, Response::HTTP_METHOD_NOT_ALLOWED, Response::HTTP_BAD_REQUEST => 10,
            Response::HTTP_UNAUTHORIZED => 100,
            default => 0,
        });

        $event->getResponse()->headers->add([
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ]);
    }
}
