<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static fn(Configuration $config): Configuration => $config
    // Symfony packages wired through config/bundles or recipes, not imported in PHP code
    ->addNamedFilter(NamedFilter::fromString('symfony/flex'))
    ->addNamedFilter(NamedFilter::fromString('symfony/runtime'))
    ->addNamedFilter(NamedFilter::fromString('symfony/dotenv'))
    ->addNamedFilter(NamedFilter::fromString('symfony/monolog-bundle'))
    ->addNamedFilter(NamedFilter::fromString('symfony/twig-bundle'))
    ->addNamedFilter(NamedFilter::fromString('symfony/web-link'))
    ->addNamedFilter(NamedFilter::fromString('symfony/webhook'))
    ->addNamedFilter(NamedFilter::fromString('runtime/frankenphp-symfony'))
    ->addNamedFilter(NamedFilter::fromString('open-telemetry/exporter-otlp'))
    ->addNamedFilter(NamedFilter::fromString('open-telemetry/opentelemetry-auto-symfony'))
    ->addNamedFilter(NamedFilter::fromString('nyholm/psr7'))
    // Replaces symfony/polyfill-iconv (see "replace" in composer.json)
    ->addNamedFilter(NamedFilter::fromString('ext-iconv'))
    // Bundles and packages wired through config/, not imported in PHP code
    ->addNamedFilter(NamedFilter::fromString('monolog/monolog'))
    ->addNamedFilter(NamedFilter::fromString('sentry/sentry-symfony'))
    ->addNamedFilter(NamedFilter::fromString('symfony/security-bundle'))
    ->addNamedFilter(NamedFilter::fromString('symfony/lock'))
    // Enables the PhpStan PropertyInfo extractor (docblock types in generated OpenAPI schemas and the serializer)
    ->addNamedFilter(NamedFilter::fromString('phpstan/phpdoc-parser'))
    ->addNamedFilter(NamedFilter::fromString('phpdocumentor/type-resolver'));
