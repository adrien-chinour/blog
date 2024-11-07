<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Config\FeatureRepository as FeatureRepositoryInterface;
use App\Infrastructure\Baserow\Http\BaserowApiClient;

final readonly class BaserowFeatureRepository implements FeatureRepositoryInterface
{
    public function __construct(
        private BaserowApiClient $apiClient
    ) {}

    public function getFeatures(): array
    {
        return $this->apiClient->getFeatures();
    }
}
