<?php

declare(strict_types=1);

namespace App\Application\Query\BatchArticle;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class BatchArticleQueryHandler
{
    use HandleTrait;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    /**
     * Identifiers that cannot be resolved (unpublished or deleted articles still referenced by the
     * search index or by another article's recommendations) are silently skipped.
     *
     * @return BlogArticle[]
     */
    public function __invoke(BatchArticleQuery $query): array
    {
        $articles = array_map(
            fn (string $identifier): mixed => $this->handle(new GetArticleQuery($identifier)),
            $query->identifiers
        );

        return array_values(
            array_filter($articles, static fn (mixed $article) => $article instanceof BlogArticle)
        );
    }
}
