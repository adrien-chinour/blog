<?php

declare(strict_types=1);

namespace App\Presentation\Http\Feature;

use App\Application\Query\GetFeatureList\GetFeatureListQuery;
use App\Domain\Config\Feature;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/features', methods: ['GET'])]
#[Cache(maxage: 10, smaxage: 60, public: true)]
#[OA\Tag(name: 'Features')]
#[OA\Response(response: 200, description: 'Feature flags with their current state', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Feature::class))))]
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
