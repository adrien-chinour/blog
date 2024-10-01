<?php

declare(strict_types=1);

namespace App\Application\Command\SearchArticleIndexation;

use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Blogging\BlogArticleSearchRepository;
use App\Domain\Blogging\Exception\BlogArticleIndexationFailedException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SearchArticleIndexationCommandHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly BlogArticleRepository $articleRepository,
        private readonly BlogArticleSearchRepository $articleSearchRepository,
    ) {}

    public function __invoke(SearchArticleIndexationCommand $command): void
    {
        if (null === ($article = $this->articleRepository->getById($command->articleId))) {
            $this->logger?->error('Try to index article {articleId} but this article does not exist.', [
                'articleId' => $command->articleId,
            ]);

            return;
        }

        try {
            $this->articleSearchRepository->index($article);
        } catch (BlogArticleIndexationFailedException $e) {
            $this->logger?->error('Fail to index article {articleId} with error : {errorMessage}.', [
                'articleId' => $command->articleId,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
