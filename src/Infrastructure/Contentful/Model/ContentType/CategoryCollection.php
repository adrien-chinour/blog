<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model\ContentType;

use App\Infrastructure\GraphQL\Attribute\CollectionOf;

final class CategoryCollection
{
    /**
     * @var Category[]
     */
    #[CollectionOf(Category::class)]
    public array $items = [];

    public function addItem(Category $blogPage): self
    {
        $this->items[] = $blogPage;

        return $this;
    }
}
