<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\If_\ExplicitNullableObjectInstanceRector;
use Rector\Config\RectorConfig;
use Rector\Instanceof_\Rector\Ternary\FlipNegatedTernaryInstanceofRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withPhpSets()
    ->withComposerBased(phpunit: true, symfony: true)
    ->withAttributesSets(symfony: true, phpunit: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, typeDeclarations: true)
    ->withImportNames(removeUnusedImports: true)
    ->withSkip([
        // keep explicit null checks, `null === $x` reads better than `!$x instanceof Foo`
        ExplicitNullableObjectInstanceRector::class,
        FlipNegatedTernaryInstanceofRector::class,
    ]);
