<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

use App\Domain\Blogging\Exception\BlogArticleIndexationFailedException;

interface BlogArticleSearchRepository
{
    /**
     * @throws BlogArticleIndexationFailedException
     */
    public function index(BlogArticle $article): void;

    /**
     * @return string[]
     */
    public function search(string $term): array;
}
