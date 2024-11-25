<?php

declare(strict_types=1);

namespace App\Tests\Factory\Repository;

use App\Domain\Layout\Page;
use App\Infrastructure\Repository\InMemoryPageRepository;

final readonly class InMemoryPageRepositoryFactory
{
    public static function create(): InMemoryPageRepository
    {
        return new InMemoryPageRepository(
            [
                '/about/' => new Page('About', '/about/', "<h2>about</h2>"),
            ]
        );
    }
}
