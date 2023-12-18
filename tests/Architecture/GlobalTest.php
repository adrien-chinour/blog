<?php

arch('All classes use strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('No dd and dump in code')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();
