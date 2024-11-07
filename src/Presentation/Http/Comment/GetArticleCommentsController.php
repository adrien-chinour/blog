<?php

declare(strict_types=1);

namespace App\Presentation\Http\Comment;

use App\Application\Query\GetArticleComments\GetArticleCommentsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles/{id}/comments', requirements: ['id' => '\w+'], methods: ['GET'])]
#[Cache(maxage: 30, smaxage: 60, public: true)]
final class GetArticleCommentsController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $id): JsonResponse
    {
        return $this->json($this->handle(new GetArticleCommentsQuery($id)));
    }
}
