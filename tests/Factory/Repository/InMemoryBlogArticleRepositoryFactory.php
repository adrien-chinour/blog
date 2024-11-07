<?php

declare(strict_types=1);

namespace App\Tests\Factory\Repository;

use App\Infrastructure\Repository\InMemoryBlogArticleRepository;
use App\Infrastructure\Repository\InMemoryBlogArticleSearchRepository;
use App\Tests\Factory\ArticleFactory;

final readonly class InMemoryBlogArticleRepositoryFactory
{
    public static function create(): InMemoryBlogArticleRepository
    {
        return new InMemoryBlogArticleRepository(self::articles());
    }

    public static function createSearch(): InMemoryBlogArticleSearchRepository
    {
        return new InMemoryBlogArticleSearchRepository(self::articles());
    }

    private static function articles(): array
    {
        return [
            '1' => ArticleFactory::create(
                id: '1',
                title: 'Gestion de messages asynchrones avec Cloud Tasks et Cloud Pub/Sub',
                slug: 'gestion-de-messages-asynchrones-avec-cloud-tasks-et-cloud-pub-sub'
            ),
            '2' => ArticleFactory::create(
                id: '2',
                title: 'Construire un site statique avec PHP',
                slug: 'construire-un-site-statique-avec-php'
            ),
        ];
    }
}
