<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model\ContentType;

use App\Infrastructure\Component\GraphQL\Attribute\CollectionOf;

final class BlogPageCollection
{
    /**
     * @var BlogPage[]
     */
    #[CollectionOf(BlogPage::class)]
    public array $items = [];

    public function addItem(BlogPage $blogPage): self
    {
        $this->items[] = $blogPage;

        return $this;
    }
}
