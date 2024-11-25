<?php

declare(strict_types=1);

namespace App\Infrastructure\Strapi\Model\ContentType;

use DateTimeImmutable;

abstract class AbstractContentType
{
    public int $id;

    public string $documentId;

    public DateTimeImmutable $createdAt;

    public DateTimeImmutable $updatedAt;

    public DateTimeImmutable $publishedAt;
}
