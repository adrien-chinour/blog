<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Strapi\Model\ContentType;

final class CommentContentType extends AbstractContentType
{
    public string $username;

    public string $articleId;

    public string $message;
}
