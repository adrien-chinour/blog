<?php

declare(strict_types=1);

namespace App\Presentation\Webhook;

use App\Application\Command\TagCacheInvalidation\TagCacheInvalidationCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contentful/publish', name: 'contentful_publish', methods: ['POST'])]
#[Cache(public: false)]
final class ContentfulPublishWebhook extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(): JsonResponse
    {
        $this->bus->dispatch(new TagCacheInvalidationCommand(['article']));

        return new JsonResponse(status: Response::HTTP_OK);
    }
}
