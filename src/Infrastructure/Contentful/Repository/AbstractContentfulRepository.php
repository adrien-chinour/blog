<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Repository;

use App\Infrastructure\Contentful\Http\ContentfulApiClient;
use App\Infrastructure\GraphQL\GraphQLQueryBuilder;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractContentfulRepository
{
    public function __construct(
        private ContentfulApiClient $apiClient,
        private SerializerInterface $serializer,
        private GraphQLQueryBuilder $queryBuilder,
    )
    {
    }

    protected function query(string $resource, array $filters = [], bool $hydrate = true): object|array|null
    {
        $result = $this->apiClient->query($this->queryBuilder->buildQuery($reflectionClass = new ReflectionClass($resource), $filters));

        if ($hydrate) {
            $result = $this->serializer->deserialize(json_encode($result[$this->queryBuilder->getName($reflectionClass)]), $resource, 'json');
        }

        return $result;
    }
}
