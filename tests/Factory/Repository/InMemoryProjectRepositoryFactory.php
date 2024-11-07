<?php

declare(strict_types=1);

namespace App\Tests\Factory\Repository;

use App\Domain\Coding\Project;
use App\Infrastructure\Repository\InMemoryProjectRepository;

final readonly class InMemoryProjectRepositoryFactory
{
    public static function create(): InMemoryProjectRepository
    {
        return new InMemoryProjectRepository([
            new Project(
                'blog',
                'https://www.github.com/adrien-chinour/blog',
                'PHP',
                'a simple blog'
            ),
            new Project(
                'wonderful-saas',
                'https://www.github.com/adrien-chinour/wonderful-saas',
                'PHP',
                'a wonderful saas making 1M$ MRR',
            ),
            new Project(
                'even',
                'https://www.github.com/adrien-chinour/even',
                'Javascript',
                'event library for Javascript (odd also available)',
            ),
            new Project(
                'odd',
                'https://www.github.com/adrien-chinour/odd',
                'Javascript',
                'odd library for Javascript (even also available)',
            ),
        ]);
    }
}
