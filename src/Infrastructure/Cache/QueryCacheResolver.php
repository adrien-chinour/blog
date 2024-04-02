<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Application\Query\QueryCache;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class QueryCacheResolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function resolve(object $query): ?QueryCacheConfig
    {
        $reflectionClass = new \ReflectionClass($query);
        if (null === ($cacheAttribute = $reflectionClass->getAttributes(QueryCache::class)[0] ?? null)) {
            return null;
        }

        return new QueryCacheConfig(
            $this->buildCacheKey($reflectionClass, $query),
            $cacheAttribute->newInstance()->ttl,
            $cacheAttribute->newInstance()->tags,
        );
    }

    private function buildCacheKey(\ReflectionClass $class, object $query): string
    {
        $properties = array_map(
            fn (\ReflectionProperty $property) => $property->getValue($query),
            $class->getProperties()
        );

        $key = md5(sprintf('%s/%s', $class->getShortName(), json_encode($properties)));

        $this->logger?->info(
            sprintf('Cache key for %s with %s is %s', $class->getShortName(), json_encode($properties), $key)
        );

        return $key;
    }
}
