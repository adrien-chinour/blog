<?php

declare(strict_types=1);

namespace App\Infrastructure\Baserow\Repository;

use App\Domain\Config\FeatureRepository as FeatureRepositoryInterface;
use App\Infrastructure\Baserow\Http\BaserowApiClient;

final readonly class FeatureRepository implements FeatureRepositoryInterface
{
    public function __construct(
        private BaserowApiClient $apiClient
    ) {}

    public function getFeatures(): array
    {
        return $this->apiClient->getFeatures();
    }
}
