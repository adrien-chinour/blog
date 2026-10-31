<?php

declare(strict_types=1);

namespace App\Presentation\Http\Project;

use App\Application\Query\GetProjectList\GetProjectListQuery;
use App\Domain\Coding\Project;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/projects', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
#[OA\Tag(name: 'Projects')]
#[OA\Response(response: 200, description: 'Public GitHub repositories of the author', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Project::class))))]
final class ListProjectController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(#[MapQueryParameter(name: 'limit')] int $limit = 10): JsonResponse
    {
        return $this->json($this->handle(new GetProjectListQuery($limit)));
    }
}
