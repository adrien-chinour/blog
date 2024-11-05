<?php

declare(strict_types=1);

namespace App\Presentation\Http\Project;

use App\Application\Query\GetProjectList\GetProjectListQuery;
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
#[Cache(maxage: 60, smaxage: 3600, public: true)]
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
