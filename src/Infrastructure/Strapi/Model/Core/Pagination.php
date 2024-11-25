<?php

declare(strict_types=1);

namespace App\Infrastructure\Strapi\Model\Core;

final class Pagination
{
    public int $page;

    public int $pageSize;

    public int $pageCount;

    public int $total;
}
