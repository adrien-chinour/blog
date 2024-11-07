<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;

final readonly class InMemoryBlogArticleRepository implements BlogArticleRepository
{
    public function __construct(
        /** @var BlogArticle[] */
        private array $articles = []
    ) {}

    public function getById(string $identifier, bool $preview = false): ?BlogArticle
    {
        return $this->articles[$identifier] ?? null;
    }

    public function getOneBy(array $filters): ?BlogArticle
    {
        $results = array_values(
            array_filter(
                $this->articles,
                function (BlogArticle $article) use ($filters) {
                    foreach ($filters as $key => $value) {
                        if (!isset($article->{$key}) || $article->{$key} !== $value) {
                            return false;
                        }
                    }

                    return true;
                }
            )
        );

        return $results[0] ?? null;
    }

    public function getList(?int $limit = null): array
    {
        return $limit === null ? $this->articles : array_slice($this->articles, 0, $limit);
    }
}
