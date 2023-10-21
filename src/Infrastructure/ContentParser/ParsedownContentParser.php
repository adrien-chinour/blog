<?php

declare(strict_types=1);

namespace App\Infrastructure\ContentParser;

/**
 * Convert markdown content to HTML
 */
final readonly class ParsedownContentParser implements ContentParserInterface
{
    public function parse(string $content): string
    {
        return (new \Parsedown())->text($content);
    }
}
