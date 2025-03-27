<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Config\Feature;
use App\Infrastructure\External\Strapi\Model\ContentType\FeatureContentType;

class StrapiFeatureFactory
{
    public function createFromModel(FeatureContentType $feature): Feature
    {
        return new Feature(
            name: $feature->name,
            description: $feature->description ?? '',
            enable: $feature->enabled,
        );
    }
}
