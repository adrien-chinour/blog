<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\OpenTelemetry;

use OpenTelemetry\API\Instrumentation\CachedInstrumentation;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SemConv\TraceAttributes;
use Symfony\Component\Serializer\SerializerInterface;
use function OpenTelemetry\Instrumentation\hook;

final class SerializerInstrumentation
{
    public static function register(): void
    {
        $instrumentation = new CachedInstrumentation(
            'custom.instrumentation.symfony_serializer',
            null,
            'https://opentelemetry.io/schemas/1.30.0',
        );

        hook(
            SerializerInterface::class,
            'serialize',
            pre: static function (
                SerializerInterface $serializer,
                array $params,
                string $class,
                string $function,
                ?string $filename,
                ?int $lineno,
            ) use ($instrumentation): array {
                $builder = $instrumentation->tracer()
                    ->spanBuilder(\sprintf('SERIALIZE %s', get_debug_type($params[0])))
                    ->setSpanKind(SpanKind::KIND_INTERNAL)
                    ->setAttribute(TraceAttributes::CODE_FUNCTION_NAME, $function)
                    ->setAttribute(TraceAttributes::CODE_NAMESPACE, $class)
                    ->setAttribute(TraceAttributes::CODE_FILEPATH, $filename)
                    ->setAttribute(TraceAttributes::CODE_LINE_NUMBER, $lineno);

                $parent = Context::getCurrent();
                $span = $builder
                    ->setParent($parent)
                    ->startSpan();

                $context = $span->storeInContext($parent);
                Context::storage()->attach($context);

                return $params;
            },
            post: static function (
                SerializerInterface $serializer,
                array $params,
                ?string $result,
                ?\Throwable $exception
            ): void {
                $scope = Context::storage()->scope();
                if (null === $scope) {
                    return;
                }

                $scope->detach();
                $span = Span::fromContext($scope->context());

                if (null !== $exception) {
                    $span->recordException($exception);
                    $span->setStatus(StatusCode::STATUS_ERROR, $exception->getMessage());
                }

                $span->end();
            },
        );

        hook(
            SerializerInterface::class,
            'deserialize',
            pre: static function (
                SerializerInterface $serializer,
                array $params,
                string $class,
                string $function,
                ?string $filename,
                ?int $lineno,
            ) use ($instrumentation): array {
                $builder = $instrumentation->tracer()
                    ->spanBuilder(\sprintf('DESERIALIZE %s', $params[1]))
                    ->setSpanKind(SpanKind::KIND_INTERNAL)
                    ->setAttribute(TraceAttributes::CODE_FUNCTION_NAME, $function)
                    ->setAttribute(TraceAttributes::CODE_NAMESPACE, $class)
                    ->setAttribute(TraceAttributes::CODE_FILEPATH, $filename)
                    ->setAttribute(TraceAttributes::CODE_LINE_NUMBER, $lineno);

                $parent = Context::getCurrent();
                $span = $builder
                    ->setParent($parent)
                    ->startSpan();

                $context = $span->storeInContext($parent);
                Context::storage()->attach($context);

                return $params;
            },
            post: static function (
                SerializerInterface $serializer,
                array $params,
                mixed $result,
                ?\Throwable $exception
            ): void {
                $scope = Context::storage()->scope();
                if (null === $scope) {
                    return;
                }

                $scope->detach();
                $span = Span::fromContext($scope->context());

                if (null !== $exception) {
                    $span->recordException($exception);
                    $span->setStatus(StatusCode::STATUS_ERROR, $exception->getMessage());
                }

                $span->end();
            },
        );
    }
}
