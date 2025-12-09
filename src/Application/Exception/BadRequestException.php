<?php

declare(strict_types=1);

namespace App\Application\Exception;

/**
 * Application exception for bad request errors.
 * This exception should always be converted to BadRequestHttpException (HTTP 400).
 */
final class BadRequestException extends \RuntimeException
{
    public static function create(string $message): self
    {
        return new self($message);
    }
}
