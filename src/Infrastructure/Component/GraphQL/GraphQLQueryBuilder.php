<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\GraphQL;

use App\Infrastructure\Component\GraphQL\Attribute\CollectionOf;
use App\Infrastructure\Component\GraphQL\Exception\GraphQLQueryBuilderException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use ReflectionClass;
use function Symfony\Component\String\s;

final class GraphQLQueryBuilder implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const MAX_QUERY_DEPTH = 10;
    private const MAX_FILTER_COUNT = 50;
    private const MAX_STRING_LENGTH = 10000;
    private const MAX_QUERY_SIZE = 50000;

    /**
     * GraphQL identifier pattern: must start with letter or underscore, followed by letters, digits, or underscores
     */
    private const IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    /**
     * @throws \ReflectionException
     * @throws GraphQLQueryBuilderException
     */
    public function buildQuery(ReflectionClass $class, array $filters = []): string
    {
        // Validate filter count
        if (count($filters) > self::MAX_FILTER_COUNT) {
            throw new GraphQLQueryBuilderException(
                sprintf('Too many filters: %d (max: %d)', count($filters), self::MAX_FILTER_COUNT)
            );
        }

        $queryName = $this->getName($class);
        $this->validateIdentifier($queryName, 'Query name');

        $filtersString = $this->buildFilters($filters, 0);
        $fieldsString = $this->buildFields($class, 0);

        $query = sprintf(
            'query {%s %s {%s}}',
            $queryName,
            $filtersString,
            $fieldsString,
        );

        // Validate query size
        if (strlen($query) > self::MAX_QUERY_SIZE) {
            throw new GraphQLQueryBuilderException(
                sprintf('Query too large: %d bytes (max: %d)', strlen($query), self::MAX_QUERY_SIZE)
            );
        }

        $this->logger?->debug(sprintf("GraphQL query: %s", $query), [
            'resource' => $class->getName(),
            'filters' => $filters
        ]);

        return $query;
    }

    /**
     * @throws GraphQLQueryBuilderException
     */
    private function buildFilters(array $filters, int $depth): string
    {
        if ($filters === []) {
            return '';
        }

        // Check depth limit
        if ($depth >= self::MAX_QUERY_DEPTH) {
            throw new GraphQLQueryBuilderException(
                sprintf('Query depth exceeded: %d (max: %d)', $depth, self::MAX_QUERY_DEPTH)
            );
        }

        $params = [];
        foreach ($filters as $key => $value) {
            if (!is_string($key)) {
                throw new GraphQLQueryBuilderException(
                    sprintf('Filter key must be a string, got: %s', get_debug_type($key))
                );
            }

            $this->validateIdentifier($key, 'Filter key');

            $filterString = $this->buildFilter($key, $value, $depth);
            if ($filterString !== '') {
                $params[] = $filterString;
            }
        }

        return sprintf('(%s)', implode(', ', $params));
    }

    /**
     * @throws GraphQLQueryBuilderException
     */
    private function buildFilter(string $name, mixed $value, int $depth): string
    {
        return match (true) {
            null === $value => '',
            is_string($value) => $this->buildStringFilter($name, $value),
            is_bool($value) => sprintf('%s: %s', $name, $value ? 'true' : 'false'),
            is_int($value) => sprintf('%s: %d', $name, $value),
            is_float($value) => sprintf('%s: %s', $name, $this->formatFloat($value)),
            is_array($value) => $this->buildArrayFilter($name, $value, $depth),
            default => throw new GraphQLQueryBuilderException(
                sprintf('Unsupported filter value type: %s for key "%s"', get_debug_type($value), $name)
            ),
        };
    }

    /**
     * Escapes a string value for GraphQL.
     * GraphQL strings must escape: ", \, \n, \r, \t, and control characters.
     */
    private function escapeGraphQLString(string $value): string
    {
        // Validate string length
        if (strlen($value) > self::MAX_STRING_LENGTH) {
            throw new GraphQLQueryBuilderException(
                sprintf('String too long: %d bytes (max: %d)', strlen($value), self::MAX_STRING_LENGTH)
            );
        }

        $escaped = '';
        $length = strlen($value);

        for ($i = 0; $i < $length; ++$i) {
            $char = $value[$i];
            $code = ord($char);

            // Escape special characters
            $escaped .= match ($char) {
                '"' => '\\"',
                '\\' => '\\\\',
                "\n" => '\\n',
                "\r" => '\\r',
                "\t" => '\\t',
                default => match (true) {
                    // Control characters (0x00-0x1F except already handled)
                    $code < 0x20 => sprintf('\\u%04X', $code),
                    // Unicode characters above 0x7F are allowed in GraphQL strings
                    default => $char,
                },
            };
        }

        return $escaped;
    }

    private function buildStringFilter(string $name, string $value): string
    {
        return sprintf('%s: "%s"', $name, $this->escapeGraphQLString($value));
    }

    /**
     * @throws GraphQLQueryBuilderException
     */
    private function buildArrayFilter(string $name, array $value, int $depth): string
    {
        // For arrays, recursively build filters with increased depth
        $nestedFilters = $this->buildFilters($value, $depth + 1);
        return sprintf('%s: %s', $name, str_replace(['(', ')'], ['{', '}'], $nestedFilters));
    }

    /**
     * Formats a float value for GraphQL, ensuring it's a valid number.
     */
    private function formatFloat(float $value): string
    {
        // Check for invalid floats (NaN, Infinity)
        if (!is_finite($value)) {
            throw new GraphQLQueryBuilderException(
                sprintf('Invalid float value: %s', var_export($value, true))
            );
        }

        // Use JSON encoding to ensure proper formatting
        $formatted = json_encode($value, JSON_UNESCAPED_SLASHES);
        if ($formatted === false) {
            throw new GraphQLQueryBuilderException('Failed to format float value');
        }

        return $formatted;
    }

    /**
     * Validates that a string is a valid GraphQL identifier.
     *
     * @throws GraphQLQueryBuilderException
     */
    private function validateIdentifier(string $identifier, string $context): void
    {
        if ($identifier === '') {
            throw new GraphQLQueryBuilderException(sprintf('%s cannot be empty', $context));
        }

        if (!preg_match(self::IDENTIFIER_PATTERN, $identifier)) {
            throw new GraphQLQueryBuilderException(
                sprintf(
                    '%s "%s" is not a valid GraphQL identifier. Must start with a letter or underscore, followed by letters, digits, or underscores.',
                    $context,
                    $identifier
                )
            );
        }
    }

    /**
     * @throws \ReflectionException
     * @throws GraphQLQueryBuilderException
     */
    private function buildFields(ReflectionClass $class, int $depth): string
    {
        // Check depth limit
        if ($depth >= self::MAX_QUERY_DEPTH) {
            throw new GraphQLQueryBuilderException(
                sprintf('Field depth exceeded: %d (max: %d)', $depth, self::MAX_QUERY_DEPTH)
            );
        }

        $fields = [];
        foreach ($class->getProperties() as $property) {
            $propertyName = $property->getName();

            // Special case for __typename (GraphQL reserved field)
            if ($propertyName === 'typename') {
                $fields[] = '__typename';
                continue;
            }

            // Validate property name is a valid identifier
            $this->validateIdentifier($propertyName, 'Property name');

            // Using CollectionOf to handle array types
            if ([] !== $property->getAttributes(CollectionOf::class)) {
                $nestedClass = new ReflectionClass(
                    $property->getAttributes(CollectionOf::class)[0]->getArguments()[0]
                );
                $fields[] = sprintf(
                    '%s {%s}',
                    $propertyName,
                    $this->buildFields($nestedClass, $depth + 1)
                );
                continue;
            }

            // Return property if it's built-in type or DateTime
            if (
                ($property->getType() instanceof \ReflectionNamedType && ($property->getType()->isBuiltin()))
                || (1 === preg_match('/^\??\\?DateTime(Immutable)?(Interface)?$/', (string)$property->getType()))
            ) {
                $fields[] = $propertyName;
                continue;
            }

            // Nested object type
            $fields[] = sprintf(
                '%s {%s}',
                $propertyName,
                $this->buildFields(new ReflectionClass((string)$property->getType()), $depth + 1)
            );
        }

        return implode(', ', $fields);
    }

    public function getName(ReflectionClass $class): string
    {
        return s(($class->getShortName()))->camel()->toString();
    }
}
