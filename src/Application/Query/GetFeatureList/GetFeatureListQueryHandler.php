<?php

declare(strict_types=1);

namespace App\Application\Query\GetFeatureList;

use App\Domain\Config\FeatureRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetFeatureListQueryHandler
{
    public function __construct(
        private FeatureRepository $featureRepository
    ) {}

    public function __invoke(GetFeatureListQuery $query): array
    {
        return $this->featureRepository->getFeatures();
    }
}
