<?php

declare(strict_types=1);

namespace App\Domain\Social;

use Symfony\Component\String\UnicodeString;

/**
 * Sanitizes comment content to prevent XSS and other security issues.
 * This is a domain service that handles content sanitization using Symfony String component.
 */
final readonly class CommentSanitizer
{
    /**
     * Sanitizes username by trimming and normalizing whitespace.
     */
    public function sanitizeUsername(string $username): string
    {
        $string = new UnicodeString($username);

        // Trim whitespace
        $sanitized = $string->trim();

        // Normalize multiple spaces to single space
        $sanitized = $sanitized->collapseWhitespace();

        return $sanitized->toString();
    }

    /**
     * Sanitizes message content by:
     * - Trimming whitespace
     * - Normalizing line breaks
     * - Removing control characters (except newlines and tabs)
     * - Limiting consecutive whitespace
     */
    public function sanitizeMessage(string $message): string
    {
        $string = new UnicodeString($message);

        // Trim whitespace
        $sanitized = $string->trim();

        // Normalize line breaks (CRLF -> LF, CR -> LF)
        $content = $sanitized->toString();
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $sanitized = new UnicodeString($content);

        // Remove control characters except newlines (\n = 0x0A) and tabs (\t = 0x09)
        // Control characters: 0x00-0x08, 0x0B-0x0C, 0x0E-0x1F, 0x7F
        $content = $sanitized->toString();
        $content = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/', '', $content);
        $sanitized = new UnicodeString($content);

        // Limit consecutive newlines (max 2 consecutive)
        $content = $sanitized->toString();
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        $sanitized = new UnicodeString($content);

        // Normalize spaces and tabs (multiple -> single space)
        // But preserve newlines for formatting
        $content = $sanitized->toString();
        // Replace multiple spaces/tabs with single space, but keep newlines
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $sanitized = new UnicodeString($content);

        return $sanitized->toString();
    }
}

