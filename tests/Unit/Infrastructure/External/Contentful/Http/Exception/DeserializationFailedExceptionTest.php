<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Contentful\Http\Exception;

use App\Infrastructure\External\Contentful\Http\Exception\DeserializationFailedException;
use PHPUnit\Framework\TestCase;

final class DeserializationFailedExceptionTest extends TestCase
{
    public function testExceptionIsRuntimeException(): void
    {
        $exception = new DeserializationFailedException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Failed to deserialize Contentful response';
        $exception = new DeserializationFailedException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Invalid JSON format';
        $code = 400;
        $exception = new DeserializationFailedException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \Exception('JSON decode error');
        $exception = new DeserializationFailedException('Deserialization failed', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}

