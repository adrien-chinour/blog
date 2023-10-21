<?php

declare(strict_types=1);

namespace App\UI\Component\Tag;

use App\Infrastructure\Contentful\Model\ContentType\Category;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('TagList')]
class TagListComponent
{
    /** @var Category[] $tags */
    public array $tags;
}
