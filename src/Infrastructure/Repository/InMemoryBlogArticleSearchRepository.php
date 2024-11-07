<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleSearchRepository;

final class InMemoryBlogArticleSearchRepository implements BlogArticleSearchRepository
{
    public function __construct(
        /** @var BlogArticle[] */
        private array $articles = [],
    ) {}

    public function index(BlogArticle $article): void
    {
        $this->articles[$article->id] = $article;
    }

    public function search(string $term): array
    {
        $term = strtolower($term);

        $results = array_filter(
            $this->articles,
            fn (BlogArticle $article) => str_contains(strtolower($article->title), $term) || str_contains(strtolower($article->content), $term) || str_contains(strtolower($article->description), $term),
        );

        return array_map(fn (BlogArticle $article) => $article->id, $results);
    }
}
