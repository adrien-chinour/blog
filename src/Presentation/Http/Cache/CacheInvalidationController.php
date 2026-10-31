<?php

declare(strict_types=1);

namespace App\Presentation\Http\Cache;

use App\Application\Command\TagCacheInvalidation\TagCacheInvalidationCommand;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route('/cache/invalidation', methods: ['GET'])]
#[Cache(public: false)]
#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: 'Cache')]
#[Security(name: 'Bearer')]
#[OA\Response(response: 200, description: 'Cache entries carrying the given tags were invalidated')]
#[OA\Response(response: 401, description: 'Missing or invalid admin token')]
final readonly class CacheInvalidationController
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(#[MapQueryParameter('tag')] array $tag): JsonResponse
    {
        $this->bus->dispatch(new TagCacheInvalidationCommand($tag));

        return new JsonResponse();
    }
}
