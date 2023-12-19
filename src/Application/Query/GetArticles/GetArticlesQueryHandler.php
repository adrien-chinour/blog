<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticles;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticlesQueryHandler
{
    public function __construct(private BlogArticleRepository $articleRepository) {}

    /**
     * @return BlogArticle[]
     */
    public function __invoke(GetArticlesQuery $query): array
    {
        return $this->articleRepository->getList($query->limit);
    }
}
