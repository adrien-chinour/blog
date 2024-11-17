<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

interface BlogArticleRepository
{
    public function getById(string $identifier, bool $published = true): ?BlogArticle;

    public function getOneBy(array $filters): ?BlogArticle;

    /**
     * @return BlogArticle[]
     */
    public function getList(?int $limit = null): array;
}
