<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Social\Comment;
use DateTimeImmutable;
use Faker\Factory;

final class CommentFactory
{
    public static function create(
        ?string $id = null,
        ?string $username = null,
        ?string $message = null,
        ?DateTimeImmutable $publishedAt = null
    ): Comment
    {
        $faker = Factory::create('fr');

        return new Comment(
            id: $id ?? uniqid(),
            username: $username ?? $faker->userName(),
            message: $message ?? $faker->paragraph(),
            publishedAt: $publishedAt ?? DateTimeImmutable::createFromMutable($faker->dateTime()),
        );
    }

    /**
     * @return Comment[]
     */
    public static function createMany(int $size = 10): array
    {
        return array_map([self::class, 'create'], range(0, $size - 1));
    }
}
