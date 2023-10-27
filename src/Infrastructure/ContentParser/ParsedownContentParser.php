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
        $content = \Parsedown::instance()->text($content);

        // add on all content links target blank and nofollow
        return str_replace('<a', '<a target="_blank" rel="nofollow"', $content);
    }
}
