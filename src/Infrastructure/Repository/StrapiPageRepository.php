<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Layout\Page;
use App\Domain\Layout\PageRepository;
use App\Infrastructure\Strapi\Http\StrapiApiClient;
use App\Infrastructure\Strapi\Model\Factory\StrapiPageFactory;

final readonly class StrapiPageRepository implements PageRepository
{
    public function __construct(
        private StrapiApiClient $apiClient,
        private StrapiPageFactory $pageFactory,
    ) {}

    public function getByPath(string $path): ?Page
    {
        $model = $this->apiClient->getPage($path);

        return $model === null ? null : $this->pageFactory->createFromModel($model);
    }
}
