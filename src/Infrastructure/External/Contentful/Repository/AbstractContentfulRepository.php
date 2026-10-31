<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Repository;

use App\Infrastructure\Component\GraphQL\GraphQLQueryBuilder;
use App\Infrastructure\External\Contentful\Http\ContentfulApiClient;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

abstract class AbstractContentfulRepository
{
    public function __construct(
        private readonly ContentfulApiClient $apiClient,
        private readonly SerializerInterface $serializer,
        private readonly GraphQLQueryBuilder $queryBuilder
    ) {}

    /**
     * @return object|array|null null when Contentful has no result for the resource (ex: unknown identifier)
     */
    protected function query(string $resource, array $filters = [], bool $hydrate = true): object|array|null
    {
        $reflectionClass = new ReflectionClass($resource);

        $result = $this->apiClient->query($this->queryBuilder->buildQuery($reflectionClass, $filters));

        $data = $result[$this->queryBuilder->getName($reflectionClass)] ?? null;

        if (!$hydrate || null === $data) {
            return $data;
        }

        $hydrated = $this->serializer->deserialize(json_encode($data), $resource, 'json');

        Assert::nullOrObject($hydrated);

        return $hydrated;
    }
}
