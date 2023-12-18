<?php

namespace App\Tests\Architecture;

arch('Infrastructure Layer cannot be used in any other layer')
    ->expect('App\Infrastructure')
    ->not->toBeUsedIn(['App\Domain', 'App\UI', 'App\Application']);

arch('UI Layer cannot be used in any other layer')
    ->expect('App\Infrastructure')
    ->not->toBeUsedIn(['App\Domain', 'App\Infrastructure', 'App\Application']);
