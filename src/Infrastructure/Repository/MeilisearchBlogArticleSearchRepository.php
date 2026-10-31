<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleSearchRepository;
use App\Domain\Blogging\Exception\BlogArticleIndexationFailedException;
use Meilisearch\Client;

final readonly class MeilisearchBlogArticleSearchRepository implements BlogArticleSearchRepository
{
    private const INDEX = 'articles';

    public function __construct(
        private Client $client,
    ) {}

    public function index(BlogArticle $article): void
    {
        try {
            $this->client
                ->index(self::INDEX)
                ->addDocuments(
                    [
                        [
                            'id' => $article->id,
                            'slug' => $article->slug,
                            'title' => $article->title,
                            'description' => $article->description,
                            // content is already rendered as HTML: index the text, not the markup
                            'content' => self::toPlainText($article->content),
                            'tags' => array_map(fn ($tag) => $tag->name, $article->tags),
                        ]
                    ],
                );
        } catch (\Exception $exception) {
            throw new BlogArticleIndexationFailedException(
                sprintf('Fail to index article %s: %s', $article->id, $exception->getMessage()),
                previous: $exception
            );
        }
    }

    public function search(string $term): array
    {
        $result = $this->client->index(self::INDEX)->search($term);

        return array_values(
            array_filter(
                array_map(fn (array $hit) => $hit['id'] ?? null, $result->getHits())
            )
        );
    }

    private static function toPlainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
