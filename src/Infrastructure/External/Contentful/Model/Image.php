<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model;

final class Image
{
    public string $url;

    public ?string $description = null;
}
