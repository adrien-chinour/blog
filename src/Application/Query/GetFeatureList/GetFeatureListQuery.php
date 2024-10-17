<?php

declare(strict_types=1);

namespace App\Application\Query\GetFeatureList;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 60)]
final readonly class GetFeatureListQuery {}