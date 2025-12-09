<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Blogging;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogTag;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class BlogArticleTest extends TestCase
{
    public function testBlogArticleCreation(): void
    {
        $publicationDate = new DateTimeImmutable('2024-01-15');
        $tag = new BlogTag('tag-php', 'PHP', 'php');
        $article = new BlogArticle(
            id: 'article-123',
            title: 'Test Article',
            description: 'Article description',
            content: 'Article content',
            imageUrl: 'https://example.com/image.jpg',
            slug: 'test-article',
            publicationDate: $publicationDate,
            tags: [$tag],
            recommendations: ['article-456'],
            imageDescription: 'Test image'
        );

        $this->assertSame('article-123', $article->id);
        $this->assertSame('Test Article', $article->title);
        $this->assertSame('Article description', $article->description);
        $this->assertSame('Article content', $article->content);
        $this->assertSame('https://example.com/image.jpg', $article->imageUrl);
        $this->assertSame('test-article', $article->slug);
        $this->assertSame($publicationDate, $article->publicationDate);
        $this->assertCount(1, $article->tags);
        $this->assertInstanceOf(BlogTag::class, $article->tags[0]);
        $this->assertCount(1, $article->recommendations);
        $this->assertSame('article-456', $article->recommendations[0]);
        $this->assertSame('Test image', $article->imageDescription);
    }

    public function testBlogArticleWithOptionalFields(): void
    {
        $publicationDate = new DateTimeImmutable();
        $article = new BlogArticle(
            id: 'article-123',
            title: 'Test Article',
            description: 'Description',
            content: 'Content',
            imageUrl: 'https://example.com/image.jpg',
            slug: 'test-article',
            publicationDate: $publicationDate
        );

        $this->assertEmpty($article->tags);
        $this->assertEmpty($article->recommendations);
        $this->assertNull($article->imageDescription);
    }

    public function testBlogArticlePropertiesArePublic(): void
    {
        $publicationDate = new DateTimeImmutable();
        $article = new BlogArticle(
            id: 'article-123',
            title: 'Test',
            description: 'Desc',
            content: 'Content',
            imageUrl: 'https://example.com/image.jpg',
            slug: 'test',
            publicationDate: $publicationDate
        );

        $this->assertIsString($article->id);
        $this->assertIsString($article->title);
        $this->assertIsString($article->description);
        $this->assertIsString($article->content);
        $this->assertIsString($article->imageUrl);
        $this->assertIsString($article->slug);
        $this->assertInstanceOf(\DateTimeInterface::class, $article->publicationDate);
        $this->assertIsArray($article->tags);
        $this->assertIsArray($article->recommendations);
    }
}

