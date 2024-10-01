<?php

declare(strict_types=1);

namespace App\Application\Command\SearchArticleInitIndexation;

use App\Application\Command\SearchArticleIndexation\SearchArticleIndexationCommand;
use App\Domain\Blogging\BlogArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class SearchArticleInitIndexationCommandHandler
{
    public function __construct(
        private BlogArticleRepository $articleRepository,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(SearchArticleInitIndexationCommand $command): void
    {
        foreach ($this->articleRepository->getList() as $article) {
            $this->messageBus->dispatch(new SearchArticleIndexationCommand($article->id));
        }
    }
}
