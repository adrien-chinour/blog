<?php

declare(strict_types=1);

namespace App\Domain\Layout;

interface PageRepository
{
    public function getByPath(string $path): ?Page;
}
