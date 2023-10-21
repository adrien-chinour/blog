<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model;

use App\Infrastructure\GraphQL\Attribute\CollectionOf;

final class ResourceCollection
{
    /**
     * @var Resource[]
     */
    #[CollectionOf(Resource::class)]
    public array $items = [];

    public function addItem(Resource $resource): self
    {
        $this->items[] = $resource;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getResourceIds(?ContentTypes $resourceTypeFilter = null): array
    {
        $items = $this->items;
        if (null !== $resourceTypeFilter) {
            $items = array_filter(
                $items,
                fn(Resource $resource) => $resource->typename === $resourceTypeFilter->value
            );
        }

        return array_map(fn(Resource $resource) => $resource->sys->id, $items);
    }
}
