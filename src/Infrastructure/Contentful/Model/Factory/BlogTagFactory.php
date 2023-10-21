<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model\Factory;

use App\Domain\Blogging\BlogTag;
use App\Infrastructure\Contentful\Model\ContentType\Category;
use App\Infrastructure\Contentful\Model\ContentType\CategoryCollection;

final readonly class BlogTagFactory
{
    public function fromCategory(Category $category): BlogTag
    {
        return new BlogTag(
            id: $category->sys->id,
            name: $category->name,
            slug: $category->slug,
        );
    }

    /**
     * @return BlogTag[]
     */
    public function fromCategoryCollection(CategoryCollection $collection): array
    {
        return array_map(fn(Category $category) => $this->fromCategory($category), $collection->items);
    }
}
