<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Article;

use App\Application\Query\GetArticleList\GetArticleListQuery;
use App\Domain\Blogging\BlogArticle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('LatestArticleList')]
final class LatestArticleListComponent
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public string $title = 'Les derniers articles.';

    public int $size = 3;

    /**
     * @return BlogArticle[]
     */
    public function articles(): array
    {
        return $this->handle(new GetArticleListQuery($this->size));
    }
}
