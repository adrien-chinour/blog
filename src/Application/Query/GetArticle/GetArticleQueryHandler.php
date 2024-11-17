<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticle;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleQueryHandler
{
    public function __construct(
        private BlogArticleRepository $blogArticleRepository,
    ) {}

    public function __invoke(GetArticleQuery $query): ?BlogArticle
    {
        return $this->blogArticleRepository->getById($query->identifier, $query->published);
    }
}
