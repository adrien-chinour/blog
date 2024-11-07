<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Blogging\BlogArticle;
use DateTimeImmutable;
use Faker\Factory;

final class ArticleFactory
{
    public static function create(
        ?string $id = null,
        ?string $title = null,
        ?string $description = null,
        ?string $content = null,
        ?string $imageUrl = null,
        ?string $slug = null,
        ?DateTimeImmutable $publicationDate = null,
        array $recommendations = [],
        array $tags = []
    ): BlogArticle
    {
        $faker = Factory::create('fr');

        return new BlogArticle(
            id: $id ?? uniqid(),
            title: $title ?? $faker->sentence(),
            description: $description ?? $faker->paragraph(),
            content: $content ?? $faker->randomHtml(),
            imageUrl: $imageUrl ?? $faker->imageUrl(),
            slug: $slug ?? $faker->slug(),
            publicationDate: $publicationDate ?? DateTimeImmutable::createFromMutable($faker->dateTime()),
            tags: $tags,
            recommendations: $recommendations,
        );
    }

    /**
     * @return BlogArticle[]
     */
    public static function createMany(int $size = 10): array
    {
        return array_map([self::class, 'create'], range(0, $size - 1));
    }
}
