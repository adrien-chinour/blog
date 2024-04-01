<?php

declare(strict_types=1);

namespace App\Infrastructure\ContentParser;

use App\Domain\Blogging\Exception\ContentRendererException;

interface ContentParserInterface
{
    /**
     * @throws ContentRendererException
     */
    public function parse(string $content): string;
}
