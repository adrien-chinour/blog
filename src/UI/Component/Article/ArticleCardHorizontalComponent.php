<?php

declare(strict_types=1);

namespace App\UI\Component\Article;

use App\Domain\Blogging\BlogArticle;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ArticleCardHorizontal')]
final class ArticleCardHorizontalComponent
{
    public BlogArticle $article;
}
