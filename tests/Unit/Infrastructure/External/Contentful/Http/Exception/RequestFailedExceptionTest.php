<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Contentful\Http\Exception;

use App\Infrastructure\External\Contentful\Http\Exception\RequestFailedException;
use PHPUnit\Framework\TestCase;

final class RequestFailedExceptionTest extends TestCase
{
    public function testExceptionIsRuntimeException(): void
    {
        $exception = new RequestFailedException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Request to Contentful failed';
        $exception = new RequestFailedException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Network error';
        $code = 500;
        $exception = new RequestFailedException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \Exception('Previous error');
        $exception = new RequestFailedException('Request failed', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}

