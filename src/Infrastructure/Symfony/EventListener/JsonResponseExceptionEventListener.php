<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Application\Exception\BadRequestException;
use App\Domain\Social\Exception\FailToPublishCommentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Throwable;

/**
 * Renders every error of this JSON API as a JSON payload, whatever the request looks like
 * (a GET without Content-Type, a browser sending "Accept: text/html"...).
 */
#[AsEventListener(ExceptionEvent::class)]
final class JsonResponseExceptionEventListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        #[Autowire(param: 'kernel.debug')]
        private readonly bool $debug = false,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $this->unwrapException($event->getThrowable());

        $errorMessage = match (true) {
            $exception instanceof BadRequestException => $exception->getMessage(),
            default => $this->debug ? $exception->getMessage() : 'An error occurred',
        };

        $data = [
            'error' => $errorMessage,
        ];

        if ($this->debug) {
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
            $exception instanceof BadRequestException => Response::HTTP_BAD_REQUEST,
            $exception instanceof HttpExceptionInterface => $this->getHttpExceptionStatusCode($exception),
            // Upstream providers (Strapi, GitHub, Contentful...) failures
            $exception instanceof HttpClientExceptionInterface,
            $exception instanceof FailToPublishCommentException => Response::HTTP_BAD_GATEWAY,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

        $event->setResponse(new JsonResponse($data, $status, $headers));
    }

    private function unwrapException(Throwable $exception): Throwable
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
        return Response::HTTP_BAD_GATEWAY;
    }
}
