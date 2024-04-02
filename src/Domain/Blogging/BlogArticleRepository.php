<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

interface BlogArticleRepository
{
    public function getById(string $identifier, bool $preview = false): ?BlogArticle;

    public function getOneBy(array $filters): ?BlogArticle;

    public function getList(?int $limit = null): array;
}
