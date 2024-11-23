<?php
declare(strict_types=1);

use App\Tests\Architecture\Rules\ApplicationRules;
use App\Tests\Architecture\Rules\LayerRules;
use Arkitect\ClassSet;
use Arkitect\CLI\Config;

return static function (Config $config): void {
    $rules = [
        ...LayerRules::rules(),
        ...ApplicationRules::rules(),
    ];

    $config
        ->add(ClassSet::fromDir(__DIR__ . '/../../src'), ...$rules);
};
