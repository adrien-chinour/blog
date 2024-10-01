<?php

declare(strict_types=1);

namespace App\Application\Query\SearchArticle;

use App\Application\Query\BatchArticle\BatchArticleQuery;
use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleSearchRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class SearchArticleQueryHandler
{
    use HandleTrait;

    public function __construct(
        private readonly BlogArticleSearchRepository $articleSearchRepository,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(SearchArticleQuery $query): array
    {
        $articles = $this->handle(
            new BatchArticleQuery($this->articleSearchRepository->search($query->term))
        );

        Assert::allIsInstanceOf($articles, BlogArticle::class);
        Assert::isArray($articles);

        return $articles;
    }
}
