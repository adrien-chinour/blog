<?php

declare(strict_types=1);

namespace App\Application\Query\GetPreviewArticle;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;

final readonly class GetPreviewArticleQueryHandler
{
    public function __construct(
        private BlogArticleRepository $blogArticleRepository,
    ) {}

    public function __invoke(GetArticleQuery $query): ?BlogArticle
    {
        return $this->blogArticleRepository->getById($query->identifier, true);
    }
}
