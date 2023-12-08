<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticleComment;

use App\Domain\Social\Comment;
use App\Domain\Social\CommentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleCommentsQueryHandler
{
    public function __construct(
        public CommentRepository $commentRepository,
    ) {}

    /**
     * @return Comment[]
     */
    public function __invoke(GetArticleCommentsQuery $query): array
    {
        return $this->commentRepository->getByArticle($query->articleId);
    }
}
