<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Layout\Page;
use App\Domain\Layout\PageRepository;

final readonly class InMemoryPageRepository implements PageRepository
{
    public function __construct(
        /** @var Page[] */
        private array $pages = [],
    ) {}

    public function getByPath(string $path): ?Page
    {
        return $this->pages[$path] ?? null;
    }
}
