<?php

declare(strict_types=1);

namespace App\Presentation\Public\Component\Article;

use App\Domain\Blogging\BlogArticle;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ArticleContent')]
final class ArticleContentComponent
{
    public BlogArticle $article;
}
