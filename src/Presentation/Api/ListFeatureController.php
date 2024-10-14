<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use App\Application\Query\GetFeatureList\GetFeatureListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/features', name: 'list_features', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 60, public: true)]
final class ListFeatureController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(): JsonResponse
    {
        return $this->json($this->handle(new GetFeatureListQuery()));
    }
}
