<?php

declare(strict_types=1);

namespace App\Domain\Config;

interface FeatureRepository
{
    /**
     * @return Feature[]
     */
    public function getFeatures(): array;
}
