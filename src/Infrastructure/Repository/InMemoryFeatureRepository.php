<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Config\Feature;
use App\Domain\Config\FeatureRepository;

final readonly class InMemoryFeatureRepository implements FeatureRepository
{
    public function __construct(
        /** @var Feature[] */
        private array $features = [],
    ) {}

    public function getFeatures(): array
    {
        return $this->features;
    }
}
