<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model;

use App\Infrastructure\Component\GraphQL\Attribute\CollectionOf;

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
                fn (Resource $resource) => $resource->typename === $resourceTypeFilter->value
            );
        }

        // array_values: array_filter keeps keys, which would serialize the list as a JSON object
        return array_values(array_map(fn (Resource $resource) => $resource->sys->id, $items));
    }
}
