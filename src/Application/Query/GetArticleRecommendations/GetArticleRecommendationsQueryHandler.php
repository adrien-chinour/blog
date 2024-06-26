<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleRecommendations;

use App\Application\Query\BatchArticle\BatchArticleQuery;
use App\Application\Query\GetArticle\GetArticleQuery;
use App\Application\Query\GetArticleList\GetArticleListQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class GetArticleRecommendationsQueryHandler
{
    use HandleTrait;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    public function __invoke(GetArticleRecommendationsQuery $query): array
    {
        $sourceArticle = $this->handle(new GetArticleQuery($query->articleIdentifier));
        if (!($sourceArticle instanceof BlogArticle)) {
            return [];
        }

        if (empty($sourceArticle->recommendations)) {
            return $this->handle(new GetArticleListQuery(2));
        }

        return $this->handle(new BatchArticleQuery($sourceArticle->recommendations));
    }
}
