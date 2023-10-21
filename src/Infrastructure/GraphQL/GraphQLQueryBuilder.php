<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL;

use App\Infrastructure\GraphQL\Attribute\CollectionOf;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use ReflectionClass;
use function Symfony\Component\String\s;

/**
 * Basic GraphQL Query Builder, it's not meant to be used in real production, it's just a proof of concept.
 */
final class GraphQLQueryBuilder implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function buildQuery(ReflectionClass $class, array $filters = []): string
    {
        // prevent injection in filters : only allow alphanumeric characters and underscore
        $filters = array_filter(
            $filters,
            fn($value, $key) => preg_match($p = '/^\w+$/', $value) && preg_match($p, $key)
        );

        $query = sprintf("query {%s %s {%s}}", $this->getName($class), $this->buildFilters($filters), $this->buildFields($class));

        $this->logger?->debug("GraphQL query: $query", [
            'resource' => $class->getName(),
            'filters' => $filters
        ]);

        return $query;
    }

    private function buildFilters(array $filters): string
    {
        if ($filters === []) {
            return '';
        }

        return sprintf(
            '(%s)',
            implode(', ', array_map(fn($key, $value) => $this->buildFilter($key, $value), array_keys($filters), $filters))
        );
    }

    private function buildFilter(string $name, mixed $value): string
    {
        return match (true) {
            null === $value => '',
            is_string($value) => sprintf('%s: "%s"', $name, $value),
            is_array($value) => sprintf('%s: %s', $name, str_replace(['(', ')'], ['{', '}'], $this->buildFilters($value))),
            default => sprintf('%s: %s', $name, $value),
        };
    }

    private function buildFields(ReflectionClass $class): string
    {
        $fields = [];
        foreach ($class->getProperties() as $property) {
            // Special case for __typename
            if ($property->getName() === 'typename') {
                $fields[] = '__typename';
                continue;
            }

            if ($property->getType()->getName() === 'array' && $property->getAttributes(CollectionOf::class) !== []) {
                $fields[] = sprintf('%s {%s}', $property->getName(), $this->buildFields(new ReflectionClass($property->getAttributes(CollectionOf::class)[0]->getArguments()[0])));
            } elseif ($property->getType()->isBuiltin() || $property->getType()->getName() === 'DateTimeInterface') {
                $fields[] = $property->getName();
            } else {
                $fields[] = sprintf('%s {%s}', $property->getName(), $this->buildFields(new ReflectionClass($property->getType()->getName())));
            }
        }

        return implode(', ', $fields);
    }

    public function getName(ReflectionClass $class): string
    {
        return s(($class->getShortName()))->camel()->toString();
    }
}
