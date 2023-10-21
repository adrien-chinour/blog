<?php

declare(strict_types=1);

namespace App\Application\Command\CacheInvalidation;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CacheInvalidationCommandHandler
{
    public function __construct(private AdapterInterface $adapter) {}

    public function __invoke(CacheInvalidationCommand $command): void
    {
        $this->adapter->deleteItem($command->cacheKey);
    }
}
