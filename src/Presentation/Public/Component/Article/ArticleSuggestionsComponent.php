<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Article;

use App\Application\Query\BatchArticle\BatchArticleQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Webmozart\Assert\Assert;

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
        $articles = $this->handle(new BatchArticleQuery($this->identifiers));

        Assert::isArray($articles);
        Assert::allIsInstanceOf($articles, BlogArticle::class);

        return $articles;
    }
}
