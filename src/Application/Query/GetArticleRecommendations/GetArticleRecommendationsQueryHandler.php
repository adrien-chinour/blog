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
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class GetArticleRecommendationsQueryHandler
{
    use HandleTrait;

    private const FALLBACK_RECOMMENDATIONS_COUNT = 2;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    /**
     * @return BlogArticle[]|null
     */
    public function __invoke(GetArticleRecommendationsQuery $query): array|null
    {
        $sourceArticle = $this->handle(new GetArticleQuery($query->articleIdentifier));
        if (!($sourceArticle instanceof BlogArticle)) {
            return null;
        }

        $articles = $sourceArticle->recommendations === []
            ? []
            : $this->handle(new BatchArticleQuery($sourceArticle->recommendations));

        // No explicit recommendation (or none of them is published anymore): fallback on latest articles
        if ($articles === []) {
            $articles = $this->handle(new GetArticleListQuery(self::FALLBACK_RECOMMENDATIONS_COUNT));
        }

        Assert::isArray($articles);
        Assert::allIsInstanceOf($articles, BlogArticle::class);

        return $articles;
    }
}
