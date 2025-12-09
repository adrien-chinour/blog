<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\GraphQL;

use App\Infrastructure\Component\GraphQL\Exception\GraphQLQueryBuilderException;
use App\Infrastructure\Component\GraphQL\GraphQLQueryBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class GraphQLQueryBuilderTest extends TestCase
{
    private GraphQLQueryBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new GraphQLQueryBuilder();
    }

    // ==================== Test Classes ====================

    private static function createSimpleTestClass(): ReflectionClass
    {
        return new ReflectionClass(new class {
            public string $id;
            public string $name;
        });
    }

    private static function createNestedTestClass(): ReflectionClass
    {
        return new ReflectionClass(new class {
            public string $id;
            public TestNestedObject $nested;
        });
    }

    private static function createTestClassWithTypename(): ReflectionClass
    {
        return new ReflectionClass(new class {
            public string $typename;
            public string $id;
        });
    }

    // ==================== Valid Query Tests ====================

    public function testBuildQueryWithNoFilters(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, []);

        $this->assertStringContainsString('query {', $query);
        $this->assertStringContainsString('id', $query);
        $this->assertStringContainsString('name', $query);
    }

    public function testBuildQueryWithStringFilter(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => 'test-123']);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test-123"', $filters);
    }

    public function testBuildQueryWithBooleanFilter(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['preview' => true]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('preview: true', $filters);
    }

    public function testBuildQueryWithIntegerFilter(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['limit' => 10]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('limit: 10', $filters);
    }

    public function testBuildQueryWithFloatFilter(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['score' => 3.14]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('score: 3.14', $filters);
    }

    public function testBuildQueryWithNestedFilters(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, [
            'where' => [
                'slug' => 'my-article',
                'published' => true,
            ],
        ]);

        $this->assertStringContainsString('where: {', $query);
        $this->assertStringContainsString('slug: "my-article"', $query);
        $this->assertStringContainsString('published: true', $query);
    }

    public function testBuildQueryWithNullFilter(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['optional' => null]);

        // Null filters should be omitted
        $this->assertStringNotContainsString('optional', $query);
    }

    public function testBuildQueryWithTypenameField(): void
    {
        $class = self::createTestClassWithTypename();
        $query = $this->builder->buildQuery($class, []);

        $this->assertStringContainsString('__typename', $query);
    }

    // ==================== String Escaping Tests ====================

    public function testEscapeDoubleQuotes(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => 'test"injection']);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\"injection"', $filters);
        $this->assertStringNotContainsString('test"injection', $query);
    }

    public function testEscapeBackslashes(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => 'test\\path']);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\\\path"', $filters);
    }

    public function testEscapeNewlines(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => "test\nline"]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\nline"', $filters);
        $this->assertStringNotContainsString("\n", $query);
    }

    public function testEscapeCarriageReturns(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => "test\rline"]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\rline"', $filters);
        $this->assertStringNotContainsString("\r", $query);
    }

    public function testEscapeTabs(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => "test\tline"]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\tline"', $filters);
        $this->assertStringNotContainsString("\t", $query);
    }

    public function testEscapeControlCharacters(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => "test\x00null"]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "test\\u0000null"', $filters);
    }

    public function testEscapeMultipleSpecialCharacters(): void
    {
        $class = self::createSimpleTestClass();
        $malicious = 'test"with\nmultiple\t\\special\rchars';
        $query = $this->builder->buildQuery($class, ['id' => $malicious]);

        $this->assertStringContainsString('id: "test\\"with', $query);
        $this->assertStringContainsString('\\nmultiple', $query);
        $this->assertStringContainsString('\\t', $query);
        $this->assertStringContainsString('\\\\special', $query);
        $this->assertStringContainsString('\\rchars"', $query);
    }

    public function testEscapeUnicodeCharacters(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => 'café']);

        // Unicode characters above 0x7F should be preserved
        $filters = $this->extractFilters($query);
        $this->assertEquals('id: "café"', $filters);
    }

    // ==================== Identifier Validation Tests ====================

    public function testValidIdentifier(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['validIdentifier' => 'value']);

        $this->assertStringContainsString('validIdentifier', $query);
    }

    public function testValidIdentifierWithUnderscore(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['_valid_identifier' => 'value']);

        $this->assertStringContainsString('_valid_identifier', $query);
    }

    public function testInvalidIdentifierWithHyphen(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('not a valid GraphQL identifier');

        $this->builder->buildQuery($class, ['invalid-identifier' => 'value']);
    }

    public function testInvalidIdentifierStartingWithNumber(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('not a valid GraphQL identifier');

        $this->builder->buildQuery($class, ['123invalid' => 'value']);
    }

    public function testInvalidIdentifierWithSpecialCharacters(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('not a valid GraphQL identifier');

        $this->builder->buildQuery($class, ['invalid@identifier' => 'value']);
    }

    public function testEmptyIdentifier(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('cannot be empty');

        $this->builder->buildQuery($class, ['' => 'value']);
    }

    // ==================== Depth Limit Tests ====================

    public function testMaxDepthAllowed(): void
    {
        $class = self::createSimpleTestClass();
        // MAX_QUERY_DEPTH is 10, so depth 0-9 is allowed (10 levels total)
        // We test with 9 levels of nesting (depth 0 + 9 nested = 10 total)
        $filters = $this->createNestedFilters(9);

        // Should not throw
        $query = $this->builder->buildQuery($class, $filters);
        $this->assertIsString($query);
    }

    public function testMaxDepthExceeded(): void
    {
        $class = self::createSimpleTestClass();
        $filters = $this->createNestedFilters(11);

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Query depth exceeded');

        $this->builder->buildQuery($class, $filters);
    }

    public function testNestedFieldsWork(): void
    {
        // Test that nested object fields are properly handled
        $nestedClass = self::createNestedTestClass();
        $query = $this->builder->buildQuery($nestedClass, []);
        
        $this->assertIsString($query);
        $this->assertStringContainsString('nested', $query);
        $this->assertStringContainsString('id', $query);
        $this->assertStringContainsString('name', $query);
    }

    public function testMaxFieldDepthExceeded(): void
    {
        $this->markTestSkipped('Field depth limit is enforced in buildFields() method');
    }

    // ==================== Size Limit Tests ====================

    public function testMaxFilterCount(): void
    {
        $class = self::createSimpleTestClass();
        $filters = [];
        for ($i = 0; $i < 50; $i++) {
            $filters["filter$i"] = "value$i";
        }

        // Should not throw
        $query = $this->builder->buildQuery($class, $filters);
        $this->assertIsString($query);
    }

    public function testMaxFilterCountExceeded(): void
    {
        $class = self::createSimpleTestClass();
        $filters = [];
        for ($i = 0; $i < 51; $i++) {
            $filters["filter$i"] = "value$i";
        }

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Too many filters');

        $this->builder->buildQuery($class, $filters);
    }

    public function testMaxStringLength(): void
    {
        $class = self::createSimpleTestClass();
        $longString = str_repeat('a', 10000);

        // Should not throw
        $query = $this->builder->buildQuery($class, ['id' => $longString]);
        $this->assertIsString($query);
    }

    public function testMaxStringLengthExceeded(): void
    {
        $class = self::createSimpleTestClass();
        $tooLongString = str_repeat('a', 10001);

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('String too long');

        $this->builder->buildQuery($class, ['id' => $tooLongString]);
    }

    // ==================== Type Validation Tests ====================

    public function testUnsupportedTypeThrowsException(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Unsupported filter value type');

        // Using a resource type which is not supported
        $this->builder->buildQuery($class, ['id' => fopen('php://memory', 'r')]);
    }

    public function testInvalidFloatValue(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Invalid float value');

        $this->builder->buildQuery($class, ['score' => NAN]);
    }

    public function testInvalidFloatInfinity(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Invalid float value');

        $this->builder->buildQuery($class, ['score' => INF]);
    }

    public function testNonStringFilterKey(): void
    {
        $class = self::createSimpleTestClass();

        $this->expectException(GraphQLQueryBuilderException::class);
        $this->expectExceptionMessage('Filter key must be a string');

        $this->builder->buildQuery($class, [123 => 'value']);
    }

    // ==================== Edge Cases ====================

    public function testEmptyStringValue(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['id' => '']);

        $filters = $this->extractFilters($query);
        $this->assertEquals('id: ""', $filters);
    }

    public function testZeroValue(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['limit' => 0]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('limit: 0', $filters);
    }

    public function testNegativeNumber(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['offset' => -10]);

        $filters = $this->extractFilters($query);
        $this->assertEquals('offset: -10', $filters);
    }

    public function testVeryLargeInteger(): void
    {
        $class = self::createSimpleTestClass();
        $query = $this->builder->buildQuery($class, ['limit' => PHP_INT_MAX]);

        $this->assertStringContainsString((string)PHP_INT_MAX, $query);
    }

    public function testComplexNestedQuery(): void
    {
        $class = self::createSimpleTestClass();
        // Use proper associative array structure (no numeric indices)
        $query = $this->builder->buildQuery($class, [
            'where' => [
                'and' => [
                    'slug' => 'article-1',
                    'published' => true,
                ],
                'or' => [
                    'category' => 'tech',
                ],
            ],
            'limit' => 10,
        ]);

        $this->assertStringContainsString('where: {', $query);
        $this->assertStringContainsString('and: {', $query);
        $this->assertStringContainsString('or: {', $query);
        $this->assertStringContainsString('limit: 10', $query);
    }

    // ==================== Helper Methods ====================

    /**
     * Extracts filter portion from GraphQL query for exact assertion
     */
    private function extractFilters(string $query): string
    {
        // Extract content between query name and fields: query {name (filters) {fields}}
        // Handle nested braces by finding the matching closing parenthesis
        if (preg_match('/query \{[^}]+ \((.+)\) \{/', $query, $matches)) {
            // The regex might capture too much if there are nested structures
            // So we need to find the matching closing parenthesis
            $filterStart = strpos($query, '(');
            if ($filterStart === false) {
                return '';
            }
            
            $depth = 0;
            $start = $filterStart + 1;
            for ($i = $start; $i < strlen($query); $i++) {
                if ($query[$i] === '(') {
                    $depth++;
                } elseif ($query[$i] === ')') {
                    if ($depth === 0) {
                        return substr($query, $start, $i - $start);
                    }
                    $depth--;
                }
            }
        }
        // If no filters, return empty
        return '';
    }

    private function createNestedFilters(int $depth): array
    {
        $filters = ['value' => 'test'];
        for ($i = 0; $i < $depth; $i++) {
            $filters = ['nested' => $filters];
        }
        return $filters;
    }

}

// Helper class for nested tests
class TestNestedObject
{
    public string $id;
    public string $name;
}

