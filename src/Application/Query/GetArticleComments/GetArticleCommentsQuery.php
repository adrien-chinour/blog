<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleComments;

use App\Application\Query\QueryCache;

#[QueryCache(ttl: 60, tags: ['comment'])]
final readonly class GetArticleCommentsQuery
{
    public function __construct(public string $articleIdentifier) {}
}
