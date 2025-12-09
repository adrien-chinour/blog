<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Application\Exception\BadRequestException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsEventListener(ExceptionEvent::class)]
final class JsonResponseExceptionEventListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(ExceptionEvent $event): void
    {
        if ($event->isMainRequest() && 'json' !== $event->getRequest()->getContentTypeFormat()) {
            return;
        }

        $exception = $this->unwrapException($event->getThrowable());

        $errorMessage = match (true) {
            $exception instanceof BadRequestException => $exception->getMessage(),
            default => $this->isDebug() ? $exception->getMessage() : 'An error occurred',
        };

        $data = [
            'error' => $errorMessage,
        ];

        if ($this->isDebug()) {
            $data['debug'] = [
                'class' => $exception::class,
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        $this->logger?->error('Exception occurred', [
            'message' => $exception->getMessage(),
            'exception' => $exception,
        ]);

        $status = match (true) {
            $exception instanceof BadRequestException => 400,
            $exception instanceof HttpExceptionInterface => $this->getHttpExceptionStatusCode($exception),
            default => 500,
        };

        $event->setResponse(new JsonResponse($data, $status));
    }

    private function unwrapException(\Throwable $exception): \Throwable
    {
        if (!$exception instanceof HandlerFailedException) {
            return $exception;
        }

        $wrappedExceptions = $exception->getWrappedExceptions();
        if (count($wrappedExceptions) === 0) {
            return $exception;
        }

        $firstException = reset($wrappedExceptions);
        if ($firstException === false) {
            return $exception;
        }

        return $firstException;
    }

    private function isDebug(): bool
    {
        return in_array($_SERVER['APP_ENV'] ?? 'prod', ['dev', 'test'], true);
    }

    /**
     * Returns status code for HTTP exceptions.
     * Client errors (4xx) are preserved, server errors (5xx) are converted to 502
     * to hide internal API call details.
     */
    private function getHttpExceptionStatusCode(HttpExceptionInterface $exception): int
    {
        $statusCode = $exception->getStatusCode();

        // Preserve client error status codes (4xx) - these are from validation/framework
        if ($statusCode >= 400 && $statusCode < 500) {
            return $statusCode;
        }

        // Convert server errors (5xx) to 502 to hide internal API call details
        return 502;
    }
}
