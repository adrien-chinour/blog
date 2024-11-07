<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleSearchRepository;
use Meilisearch\Client;

final readonly class MeilisearchBlogArticleSearchRepository implements BlogArticleSearchRepository
{
    public function __construct(
        private Client $client,
    ) {}

    public function index(BlogArticle $article): void
    {
        $this->client
            ->index('articles')
            ->addDocuments(
                [
                    [
                        'id' => $article->id,
                        'slug' => $article->slug,
                        'title' => $article->title,
                        'description' => $article->description,
                        'content' => $article->content,
                        'tags' => array_map(fn ($tag) => $tag->name, $article->tags),
                    ]
                ],
            );
    }

    public function search(string $term): array
    {
        $result = $this->client->index('articles')->search($term);

        return array_values(
            array_filter(
                array_map(fn ($hit) => $hit['id'] ?? null, $result->getHits())
            )
        );
    }
}
