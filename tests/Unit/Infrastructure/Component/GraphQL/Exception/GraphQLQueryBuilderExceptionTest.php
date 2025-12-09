<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\GraphQL\Exception;

use App\Infrastructure\Component\GraphQL\Exception\GraphQLQueryBuilderException;
use PHPUnit\Framework\TestCase;

final class GraphQLQueryBuilderExceptionTest extends TestCase
{
    public function testExceptionIsRuntimeException(): void
    {
        $exception = new GraphQLQueryBuilderException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'GraphQL query building failed';
        $exception = new GraphQLQueryBuilderException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Invalid query';
        $code = 500;
        $exception = new GraphQLQueryBuilderException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \Exception('Previous error');
        $exception = new GraphQLQueryBuilderException('Query failed', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}

