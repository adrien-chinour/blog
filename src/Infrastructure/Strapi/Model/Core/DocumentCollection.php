<?php

declare(strict_types=1);

namespace App\Infrastructure\Strapi\Model\Core;

final class DocumentCollection
{
    /**
     * List of document (ContentType)
     */
    public array $data;

    public DocumentCollectionMeta $meta;
}
