<?php

declare(strict_types=1);

namespace App\UI\Component\Article;

use App\Domain\Blogging\BlogArticle;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ArticleContent')]
final class ArticleContentComponent
{
    public BlogArticle $article;
}
