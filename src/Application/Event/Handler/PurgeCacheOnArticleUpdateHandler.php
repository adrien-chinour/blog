<?php

declare(strict_types=1);

namespace App\Application\Event\Handler;

use App\Application\Command\TagCacheInvalidation\TagCacheInvalidationCommand;
use App\Application\Event\Article\ArticlePublishedEvent;
use App\Application\Event\Article\ArticleUnpublishedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class PurgeCacheOnArticleUpdateHandler
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(ArticleUnpublishedEvent|ArticlePublishedEvent $event): void
    {
        $this->bus->dispatch(new TagCacheInvalidationCommand(['article']));
    }
}
