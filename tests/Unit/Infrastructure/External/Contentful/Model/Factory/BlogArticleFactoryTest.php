<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Contentful\Model\Factory;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogTag;
use App\Infrastructure\Component\ContentParser\ContentParserInterface;
use App\Infrastructure\External\Contentful\Model\ContentType\BlogPage;
use App\Infrastructure\External\Contentful\Model\ContentType\BlogPageCollection;
use App\Infrastructure\External\Contentful\Model\ContentType\CategoryCollection;
use App\Infrastructure\External\Contentful\Model\ContentTypes;
use App\Infrastructure\External\Contentful\Model\Factory\BlogArticleFactory;
use App\Infrastructure\External\Contentful\Model\Factory\BlogTagFactory;
use App\Infrastructure\External\Contentful\Model\Image;
use App\Infrastructure\External\Contentful\Model\Resource;
use App\Infrastructure\External\Contentful\Model\ResourceCollection;
use App\Infrastructure\External\Contentful\Model\System;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BlogArticleFactoryTest extends TestCase
{
    private ContentParserInterface&MockObject $contentParser;
    private BlogTagFactory&MockObject $blogTagFactory;
    private BlogArticleFactory $factory;

    protected function setUp(): void
    {
        $this->contentParser = $this->createMock(ContentParserInterface::class);
        $this->blogTagFactory = $this->createMock(BlogTagFactory::class);
        $this->factory = new BlogArticleFactory($this->contentParser, $this->blogTagFactory);
    }

    public function testFromBlogPageCreatesBlogArticle(): void
    {
        $blogPage = $this->createBlogPage();
        $tags = [new BlogTag('tag-1', 'PHP', 'php')];

        $this->contentParser->expects($this->once())
            ->method('parse')
            ->with('Raw markdown content')
            ->willReturn('<p>Parsed HTML content</p>');

        $this->blogTagFactory->expects($this->once())
            ->method('fromCategoryCollection')
            ->willReturn($tags);

        $article = $this->factory->fromBlogPage($blogPage);

        $this->assertInstanceOf(BlogArticle::class, $article);
        $this->assertSame('entry-123', $article->id);
        $this->assertSame('Test Article', $article->title);
        $this->assertSame('Article description', $article->description);
        $this->assertSame('<p>Parsed HTML content</p>', $article->content);
        $this->assertSame('https://example.com/image.jpg', $article->imageUrl);
        $this->assertSame('test-article', $article->slug);
        $this->assertCount(1, $article->tags);
        $this->assertInstanceOf(BlogTag::class, $article->tags[0]);
    }

    public function testFromBlogPageHandlesNullFirstPublishedAt(): void
    {
        $blogPage = $this->createBlogPage();
        $blogPage->sys->firstPublishedAt = null;

        $this->contentParser->method('parse')->willReturn('Parsed');
        $this->blogTagFactory->method('fromCategoryCollection')->willReturn([]);

        $article = $this->factory->fromBlogPage($blogPage);

        $this->assertInstanceOf(\DateTimeInterface::class, $article->publicationDate);
    }

    public function testFromBlogPageHandlesImageDescription(): void
    {
        $blogPage = $this->createBlogPage();
        $blogPage->image->description = '<a href="test">Link</a>';

        $this->contentParser->method('parse')->willReturn('Parsed');
        $this->blogTagFactory->method('fromCategoryCollection')->willReturn([]);

        $article = $this->factory->fromBlogPage($blogPage);

        $this->assertStringContainsString('target="_blank"', $article->imageDescription);
    }

    public function testFromBlogPageHandlesNullImageDescription(): void
    {
        $blogPage = $this->createBlogPage();
        $blogPage->image->description = null;

        $this->contentParser->method('parse')->willReturn('Parsed');
        $this->blogTagFactory->method('fromCategoryCollection')->willReturn([]);

        $article = $this->factory->fromBlogPage($blogPage);

        $this->assertNull($article->imageDescription);
    }

    public function testFromBlogPageCollectionCreatesArrayOfArticles(): void
    {
        $blogPage1 = $this->createBlogPage('entry-1', 'Article 1');
        $blogPage2 = $this->createBlogPage('entry-2', 'Article 2');
        $collection = new BlogPageCollection();
        $collection->items = [$blogPage1, $blogPage2];

        $this->contentParser->expects($this->exactly(2))
            ->method('parse')
            ->willReturn('Parsed');
        $this->blogTagFactory->expects($this->exactly(2))
            ->method('fromCategoryCollection')
            ->willReturn([]);

        $articles = $this->factory->fromBlogPageCollection($collection);

        $this->assertIsArray($articles);
        $this->assertCount(2, $articles);
        $this->assertInstanceOf(BlogArticle::class, $articles[0]);
        $this->assertInstanceOf(BlogArticle::class, $articles[1]);
        $this->assertSame('entry-1', $articles[0]->id);
        $this->assertSame('entry-2', $articles[1]->id);
    }

    public function testFromBlogPageCollectionHandlesEmptyCollection(): void
    {
        $collection = new BlogPageCollection();
        $collection->items = [];

        $articles = $this->factory->fromBlogPageCollection($collection);

        $this->assertIsArray($articles);
        $this->assertEmpty($articles);
    }

    private function createBlogPage(string $id = 'entry-123', string $title = 'Test Article'): BlogPage
    {
        $sys = new System();
        $sys->id = $id;
        $sys->firstPublishedAt = new DateTimeImmutable('2024-01-15');

        $image = new Image();
        $image->url = 'https://example.com/image.jpg';
        $image->description = null;

        $categoryCollection = new CategoryCollection();
        $categoryCollection->items = [];

        $recommendationsCollection = new ResourceCollection();
        $recommendationsCollection->items = [];

        $blogPage = new BlogPage();
        $blogPage->sys = $sys;
        $blogPage->title = $title;
        $blogPage->description = 'Article description';
        $blogPage->slug = 'test-article';
        $blogPage->content = 'Raw markdown content';
        $blogPage->image = $image;
        $blogPage->categoriesCollection = $categoryCollection;
        $blogPage->recommendationsCollection = $recommendationsCollection;

        return $blogPage;
    }
}

