<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Contentful\Model\Factory;

use App\Domain\Blogging\BlogTag;
use App\Infrastructure\External\Contentful\Model\ContentType\Category;
use App\Infrastructure\External\Contentful\Model\ContentType\CategoryCollection;
use App\Infrastructure\External\Contentful\Model\Factory\BlogTagFactory;
use App\Infrastructure\External\Contentful\Model\System;
use PHPUnit\Framework\TestCase;

final class BlogTagFactoryTest extends TestCase
{
    private BlogTagFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BlogTagFactory();
    }

    public function testFromCategoryCreatesBlogTag(): void
    {
        $category = $this->createCategory('tag-123', 'PHP', 'php');

        $tag = $this->factory->fromCategory($category);

        $this->assertInstanceOf(BlogTag::class, $tag);
        $this->assertSame('tag-123', $tag->id);
        $this->assertSame('PHP', $tag->name);
        $this->assertSame('php', $tag->slug);
    }

    public function testFromCategoryCollectionCreatesArrayOfTags(): void
    {
        $category1 = $this->createCategory('tag-1', 'PHP', 'php');
        $category2 = $this->createCategory('tag-2', 'Symfony', 'symfony');
        $collection = new CategoryCollection();
        $collection->items = [$category1, $category2];

        $tags = $this->factory->fromCategoryCollection($collection);

        $this->assertIsArray($tags);
        $this->assertCount(2, $tags);
        $this->assertInstanceOf(BlogTag::class, $tags[0]);
        $this->assertInstanceOf(BlogTag::class, $tags[1]);
        $this->assertSame('tag-1', $tags[0]->id);
        $this->assertSame('tag-2', $tags[1]->id);
    }

    public function testFromCategoryCollectionHandlesEmptyCollection(): void
    {
        $collection = new CategoryCollection();
        $collection->items = [];

        $tags = $this->factory->fromCategoryCollection($collection);

        $this->assertIsArray($tags);
        $this->assertEmpty($tags);
    }

    private function createCategory(string $id, string $name, string $slug): Category
    {
        $sys = new System();
        $sys->id = $id;
        
        $category = new Category();
        $category->sys = $sys;
        $category->name = $name;
        $category->slug = $slug;
        
        return $category;
    }
}

