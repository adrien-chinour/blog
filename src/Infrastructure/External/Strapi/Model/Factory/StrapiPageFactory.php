<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Layout\Page;
use App\Infrastructure\ContentParser\ContentParserInterface;
use App\Infrastructure\External\Strapi\Model\ContentType\PageContentType;

final readonly class StrapiPageFactory
{
    public function __construct(
        private ContentParserInterface $contentParser
    ) {}

    public function createFromModel(PageContentType $page): Page
    {
        return new Page(
            title: $page->title,
            path: $page->url,
            content: $this->contentParser->parse($page->content ?? ''),
        );
    }
}
