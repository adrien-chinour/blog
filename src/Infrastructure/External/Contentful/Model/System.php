<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Model;

use DateTimeInterface;

final class System
{
    public string $id;

    public ?DateTimeInterface $publishedAt = null;

    public ?DateTimeInterface $firstPublishedAt = null;

    public ?string $spaceId = null;

    public ?string $environmentId = null;

    public ?int $publishedVersion = null;
}
