<?php

namespace App\Tests\Architecture;

const INFRASTRUCTURE_LAYER = 'App\Infrastructure';
const DOMAIN_LAYER = 'App\Domain';
const APPLICATION_LAYER = 'App\Application';
const UI_LAYER = 'App\UI';

arch('Infrastructure Layer cannot be used in any other layer')
    ->expect(INFRASTRUCTURE_LAYER)
    ->not->toBeUsedIn([DOMAIN_LAYER, UI_LAYER, APPLICATION_LAYER]);

arch('UI Layer cannot be used in any other layer')
    ->expect(INFRASTRUCTURE_LAYER)
    ->not->toBeUsedIn([DOMAIN_LAYER, INFRASTRUCTURE_LAYER, APPLICATION_LAYER]);
