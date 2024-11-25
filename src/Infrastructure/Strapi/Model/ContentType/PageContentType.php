<?php

declare(strict_types=1);

namespace App\Infrastructure\Strapi\Model\ContentType;

final class PageContentType extends AbstractContentType
{
    public string $title;

    public string $url;

    public ?string $content = null;
}
