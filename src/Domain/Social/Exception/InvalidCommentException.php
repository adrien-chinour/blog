<?php

declare(strict_types=1);

namespace App\Domain\Social\Exception;

/**
 * Exception thrown when comment data is invalid.
 */
final class InvalidCommentException extends \DomainException
{
    public static function invalidArticle(string $articleIdentifier): self
    {
        return new self(sprintf('Article with identifier "%s" does not exist or is not published.', $articleIdentifier));
    }

    public static function invalidUsername(string $reason): self
    {
        return new self(sprintf('Invalid username: %s', $reason));
    }

    public static function invalidMessage(string $reason): self
    {
        return new self(sprintf('Invalid message: %s', $reason));
    }

    public static function messageTooLong(int $length, int $maxLength): self
    {
        return new self(sprintf('Message is too long: %d characters (max: %d)', $length, $maxLength));
    }

    public static function usernameTooLong(int $length, int $maxLength): self
    {
        return new self(sprintf('Username is too long: %d characters (max: %d)', $length, $maxLength));
    }

    public static function messageTooShort(int $length, int $minLength): self
    {
        return new self(sprintf('Message is too short: %d characters (min: %d)', $length, $minLength));
    }

    public static function usernameTooShort(int $length, int $minLength): self
    {
        return new self(sprintf('Username is too short: %d characters (min: %d)', $length, $minLength));
    }

    public static function containsProhibitedContent(string $reason): self
    {
        return new self(sprintf('Content contains prohibited material: %s', $reason));
    }
}
