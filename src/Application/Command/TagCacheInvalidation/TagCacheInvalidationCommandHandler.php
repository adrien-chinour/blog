<?php

declare(strict_types=1);

namespace App\Application\Command\TagCacheInvalidation;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsMessageHandler]
final readonly class TagCacheInvalidationCommandHandler
{
    public function __construct(
        private TagAwareCacheInterface $messengerCache
    ) {}

    public function __invoke(TagCacheInvalidationCommand $command): void
    {
        $this->messengerCache->invalidateTags($command->tags);
    }
}
