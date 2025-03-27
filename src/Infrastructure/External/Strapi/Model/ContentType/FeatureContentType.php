<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Model\ContentType;

final class FeatureContentType extends AbstractContentType
{
    public string $name;

    public bool $enabled;

    public ?string $description = null;
}
