<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Config\Feature;
use App\Domain\Config\FeatureRepository as FeatureRepositoryInterface;
use App\Infrastructure\External\Strapi\Http\StrapiApiClient;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiFeatureFactory;

final readonly class StrapiFeatureRepository implements FeatureRepositoryInterface
{
    public function __construct(
        private StrapiApiClient $apiClient,
        private StrapiFeatureFactory $featureFactory,
    ) {}

    /**
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        return array_map(
            $this->featureFactory->createFromModel(...),
            $this->apiClient->getFeatures(),
        );
    }
}
