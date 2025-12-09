<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\GraphQL\Exception;

/**
 * Exception thrown when GraphQL query building fails due to invalid input or security constraints.
 */
final class GraphQLQueryBuilderException extends \RuntimeException {}
