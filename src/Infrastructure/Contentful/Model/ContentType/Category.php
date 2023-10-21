<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model\ContentType;

use App\Infrastructure\Contentful\Model\System;

final class Category
{
    public System $sys;

    public string $name;

    public string $slug;
}
