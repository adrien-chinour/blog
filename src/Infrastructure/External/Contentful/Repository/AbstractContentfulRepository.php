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

    protected function query(string $resource, array $filters = [], bool $hydrate = true): object|array|null
    {
        $result = $this->apiClient->query(
            $this->queryBuilder->buildQuery($reflectionClass = new ReflectionClass($resource), $filters)
        );

        if ($hydrate) {
            $result = $this->serializer->deserialize(
                json_encode($result[$this->queryBuilder->getName($reflectionClass)]),
                $resource,
                'json',
            );

            Assert::nullOrObject($result);
        }

        return $result;
    }
}
