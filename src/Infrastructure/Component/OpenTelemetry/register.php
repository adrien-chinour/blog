<?php

declare(strict_types=1);

use App\Infrastructure\Component\OpenTelemetry\SerializerInstrumentation;
use OpenTelemetry\SDK\Sdk;

if (class_exists(Sdk::class) && Sdk::isDisabled()) {
    return;
}

if (extension_loaded('opentelemetry') === false) {
    trigger_error('The opentelemetry extension must be loaded in order to autoload the OpenTelemetry Symfony auto-instrumentation', E_USER_WARNING);

    return;
}

SerializerInstrumentation::register();
