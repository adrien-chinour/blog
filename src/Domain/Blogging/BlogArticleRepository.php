<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

interface BlogArticleRepository
{
    public function getLatestArticles(int $length): array;

    public function getById(string $identifier): ?BlogArticle;

    public function getOneBy(array $filters): ?BlogArticle;

    public function getAll(): array;
}
