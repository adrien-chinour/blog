<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\GraphQL\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class CollectionOf
{
    public function __construct(public string $type) {}
}
