<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Blogging\BlogArticle;
use Faker\Factory;

final class ArticleFactory
{
    public static function create(array $recommendations = [], array $tags = []): BlogArticle
    {
        $faker = Factory::create('fr');

        return new BlogArticle(
            id: uniqid(),
            title: $faker->sentence(),
            description: $faker->paragraph(),
            content: $faker->randomHtml(),
            imageUrl: $faker->imageUrl(),
            slug: $faker->slug(),
            publicationDate: \DateTimeImmutable::createFromMutable($faker->dateTime()),
            tags: $tags,
            recommendations: $recommendations,
        );
    }

    /**
     * @return BlogArticle[]
     */
    public static function createMany(int $size = 10): array
    {
        return array_map(fn () => self::create(), range(0, $size - 1));
    }
}
