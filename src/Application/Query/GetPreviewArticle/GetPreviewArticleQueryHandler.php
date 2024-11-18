<?php

declare(strict_types=1);

namespace App\Application\Query\GetPreviewArticle;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetPreviewArticleQueryHandler
{
    public function __construct(
        private BlogArticleRepository $blogArticleRepository,
    ) {}

    public function __invoke(GetPreviewArticleQuery $query): ?BlogArticle
    {
        return $this->blogArticleRepository->getById($query->identifier, false);
    }
}
