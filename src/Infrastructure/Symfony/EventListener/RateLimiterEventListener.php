<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class RateLimiterEventListener
{
    /**
     * Header set by Cloudflare with the real client IP. It is only trustworthy when the request
     * actually comes from a trusted proxy (see framework.trusted_proxies), otherwise anyone could
     * spoof it and get a fresh limiter on every request.
     */
    private const CLOUDFLARE_IP_HEADER = 'Cf-Connecting-Ip';

    public function __construct(
        private RateLimiterFactoryInterface $publicLimiter,
        private AuthorizationCheckerInterface $checker
    ) {}

    public function onRequest(RequestEvent $event): void
    {
        if ($this->isExempted($event)) {
            return;
        }

        $limiter = $this->getLimiter($event->getRequest());
        if (false === $limiter->consume()->isAccepted()) {
            $event->setResponse(new Response(status: Response::HTTP_TOO_MANY_REQUESTS));
        }
    }

    public function onResponse(ResponseEvent $event): void
    {
        if ($this->isExempted($event)) {
            return;
        }

        $limiter = $this->getLimiter($event->getRequest());

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

    private function isExempted(KernelEvent $event): bool
    {
        return false === $event->isMainRequest() || $this->checker->isGranted('ROLE_ADMIN');
    }

    private function getLimiter(Request $request): LimiterInterface
    {
        $ip = $request->getClientIp();

        if ($request->isFromTrustedProxy()) {
            $ip = $request->headers->get(self::CLOUDFLARE_IP_HEADER) ?? $ip;
        }

        return $this->publicLimiter->create($ip ?? 'unknown');
    }
}
