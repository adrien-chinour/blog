<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Article;

use App\Application\Query\GetArticleList\GetArticleListQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Webmozart\Assert\Assert;

#[AsTwigComponent('ArticleList')]
final class ArticleListComponent
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return BlogArticle[]
     */
    public function articles(): array
    {
        $articles = $this->handle(new GetArticleListQuery());

        Assert::isArray($articles);
        Assert::allIsInstanceOf($articles, BlogArticle::class);

        return $articles;
    }
}
