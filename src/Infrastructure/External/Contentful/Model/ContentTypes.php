<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model;

enum ContentTypes: string
{
    case Category = 'Category';

    case BlogPage = 'BlogPage';
}
