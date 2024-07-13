<?php

declare(strict_types=1);

namespace App\Application\Query\BatchArticle;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class BatchArticleQueryHandler
{
    use HandleTrait;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    /**
     * @return BlogArticle[]
     */
    public function __invoke(BatchArticleQuery $query): array
    {
        $articles = array_map(
            fn (string $identifier) => $this->handle(new GetArticleQuery($identifier)),
            $query->identifiers
        );

        Assert::allIsInstanceOf($articles, BlogArticle::class);

        return $articles;
    }
}
