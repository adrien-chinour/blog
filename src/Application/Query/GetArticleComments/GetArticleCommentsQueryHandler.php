<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleComments;

use App\Domain\Social\CommentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleCommentsQueryHandler
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(GetArticleCommentsQuery $query): array
    {
        return $this->commentRepository->getArticleComments($query->articleIdentifier);
    }
}
