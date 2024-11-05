<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(ExceptionEvent::class)]
final class JsonResponseExceptionEventListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(ExceptionEvent $event): void
    {
        if ($event->isMainRequest() && 'json' !== $event->getRequest()->getContentTypeFormat()) {
            return;
        }


        $response = ($exception = $event->getThrowable()) instanceof HttpExceptionInterface
            ? new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode())
            : new JsonResponse(null, 500);


        $this->logger?->error($exception->getMessage());

        $event->setResponse($response);
    }
}
