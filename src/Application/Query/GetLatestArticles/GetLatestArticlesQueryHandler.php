<?php

declare(strict_types=1);

namespace App\Application\Query\GetLatestArticles;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetLatestArticlesQueryHandler
{
    public function __construct(private BlogArticleRepository $blogArticleRepository) {}

    /**
     * @param GetLatestArticlesQuery $query
     * @return BlogArticle[]
     */
    public function __invoke(GetLatestArticlesQuery $query): array
    {
        return $this->blogArticleRepository->getLatestArticles($query->limit);
    }
}
