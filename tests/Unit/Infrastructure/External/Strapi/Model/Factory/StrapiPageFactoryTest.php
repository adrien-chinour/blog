<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Layout\Page;
use App\Infrastructure\Component\ContentParser\ContentParserInterface;
use App\Infrastructure\External\Strapi\Model\ContentType\PageContentType;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiPageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StrapiPageFactoryTest extends TestCase
{
    private ContentParserInterface&MockObject $contentParser;
    private StrapiPageFactory $factory;

    protected function setUp(): void
    {
        $this->contentParser = $this->createMock(ContentParserInterface::class);
        $this->factory = new StrapiPageFactory($this->contentParser);
    }

    public function testCreateFromModelCreatesPage(): void
    {
        $pageContentType = $this->createPageContentType('Test Page', '/test', '# Markdown content');

        $this->contentParser->expects($this->once())
            ->method('parse')
            ->with('# Markdown content')
            ->willReturn('<h1>Markdown content</h1>');

        $page = $this->factory->createFromModel($pageContentType);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertSame('Test Page', $page->title);
        $this->assertSame('/test', $page->path);
        $this->assertSame('<h1>Markdown content</h1>', $page->content);
    }

    public function testCreateFromModelHandlesNullContent(): void
    {
        $pageContentType = $this->createPageContentType('Test Page', '/test', null);

        $this->contentParser->expects($this->once())
            ->method('parse')
            ->with('')
            ->willReturn('');

        $page = $this->factory->createFromModel($pageContentType);

        $this->assertSame('', $page->content);
    }

    private function createPageContentType(string $title, string $url, ?string $content): PageContentType
    {
        $page = new PageContentType();
        $page->title = $title;
        $page->url = $url;
        $page->content = $content;

        return $page;
    }
}

