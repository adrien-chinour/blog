<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Article;

use App\Application\Query\BatchArticle\BatchArticleQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ArticleSuggestions')]
final class ArticleSuggestionsComponent
{
    use HandleTrait;

    public array $identifiers;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    /**
     * @return BlogArticle[]
     */
    public function articles(): array
    {
        return $this->handle(new BatchArticleQuery($this->identifiers));
    }
}
