<?php

declare(strict_types=1);

namespace App\Infrastructure\ContentParser;

interface ContentParserInterface
{
    public function parse(string $content): string;
}
