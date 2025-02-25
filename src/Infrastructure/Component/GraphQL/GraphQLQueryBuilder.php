<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\GraphQL;

use App\Infrastructure\Component\GraphQL\Attribute\CollectionOf;
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

    /**
     * @throws \ReflectionException
     */
    public function buildQuery(ReflectionClass $class, array $filters = []): string
    {
        $query = sprintf(
            'query {%s %s {%s}}',
            $this->getName($class),
            $this->buildFilters($filters),
            $this->buildFields($class),
        );

        $this->logger?->debug(sprintf("GraphQL query: %s", $query), [
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

        $params = implode(
            ', ',
            array_map(fn ($key, $value) => $this->buildFilter($key, $value), array_keys($filters), $filters)
        );

        return sprintf('(%s)', $params);
    }

    private function buildFilter(string $name, mixed $value): string
    {
        return match (true) {
            null === $value => '',
            is_string($value) => sprintf(
                '%s: "%s"',
                $name,
                $value,
            ),
            is_bool($value) => sprintf(
                '%s: %s',
                $name,
                $value ? 'true' : 'false',
            ),
            is_array($value) => sprintf(
                '%s: %s',
                $name,
                str_replace(['(', ')'], ['{', '}'], $this->buildFilters($value)),
            ),
            default => sprintf('%s: %s', $name, $value),
        };
    }

    /**
     * @throws \ReflectionException
     */
    private function buildFields(ReflectionClass $class): string
    {
        $fields = [];
        foreach ($class->getProperties() as $property) {
            // Special case for __typename
            if ($property->getName() === 'typename') {
                $fields[] = '__typename';
                continue;
            }

            // Using CollectionOf to handle array types
            if ([] !== $property->getAttributes(CollectionOf::class)) {
                $fields[] = sprintf(
                    '%s {%s}',
                    $property->getName(),
                    $this->buildFields(
                        new ReflectionClass($property->getAttributes(CollectionOf::class)[0]->getArguments()[0])
                    )
                );
                continue;
            }

            // Return property if it's build-in type or Datetime
            if (
                ($property->getType() instanceof \ReflectionNamedType && ($property->getType()->isBuiltin()))
                || (1 === preg_match('/^\??\\?DateTime(Immutable)?(Interface)?$/', (string)$property->getType()))
            ) {
                $fields[] = $property->getName();
                continue;
            }

            $fields[] = sprintf(
                '%s {%s}',
                $property->getName(),
                $this->buildFields(new ReflectionClass((string)$property->getType()))
            );
        }

        return implode(', ', $fields);
    }

    public function getName(ReflectionClass $class): string
    {
        return s(($class->getShortName()))->camel()->toString();
    }
}
