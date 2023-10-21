<?php

declare(strict_types=1);

namespace App\UI\Controller\Admin;

use App\Application\Command\CacheInvalidation\CacheInvalidationCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/admin/cache-invalidation', name: 'admin_cache_invalidation', methods: ['GET'])]
#[Cache(public: false)]
final readonly class CacheInvalidationController
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(#[MapQueryParameter('cacheKey')] string $cacheKey): JsonResponse
    {
        $this->bus->dispatch(new CacheInvalidationCommand($cacheKey));

        return new JsonResponse();
    }
}
