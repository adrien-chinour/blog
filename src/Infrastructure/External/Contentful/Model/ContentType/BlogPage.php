<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model\ContentType;

use App\Infrastructure\External\Contentful\Model\Image;
use App\Infrastructure\External\Contentful\Model\ResourceCollection;
use App\Infrastructure\External\Contentful\Model\System;

final class BlogPage
{
    public string $title;

    public string $description;

    public string $slug;

    public string $content;

    public Image $image;

    public System $sys;

    public ResourceCollection $recommendationsCollection;

    public CategoryCollection $categoriesCollection;
}
