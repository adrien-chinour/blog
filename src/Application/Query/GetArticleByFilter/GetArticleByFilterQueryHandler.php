<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleByFilter;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleByFilterQueryHandler
{
    public function __construct(private BlogArticleRepository $articleRepository)
    {
    }

    public function __invoke(GetArticleByFilterQuery $query): ?BlogArticle
    {
        return $this->articleRepository->getOneBy($query->filters);
    }
}
