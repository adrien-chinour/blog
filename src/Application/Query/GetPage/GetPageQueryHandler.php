<?php

declare(strict_types=1);

namespace App\Application\Query\GetPage;

use App\Domain\Layout\Page;
use App\Domain\Layout\PageRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetPageQueryHandler
{
    public function __construct(
        private PageRepository $repository
    ) {}

    public function __invoke(GetPageQuery $query): ?Page
    {
        return $this->repository->getByPath($query->path);
    }
}
