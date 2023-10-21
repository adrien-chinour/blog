<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model;

enum ContentTypes: string
{
    case Category = 'Category';

    case BlogPage = 'BlogPage';
}
