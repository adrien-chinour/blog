<?php

declare(strict_types=1);

namespace App\UI\Component\Article;

use App\Application\Query\GetArticles\GetArticlesQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

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
        return $this->handle(new GetArticlesQuery());
    }
}
