<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Tag;

use App\Domain\Blogging\BlogTag;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('TagList')]
class TagListComponent
{
    /** @var BlogTag[] $tags */
    public array $tags;
}
