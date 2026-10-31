<?php

declare(strict_types=1);

namespace App\Presentation\Http\Comment;

use App\Application\Query\GetArticleComments\GetArticleCommentsQuery;
use App\Domain\Social\Comment;
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
#[Route(path: '/articles/{id}/comments', requirements: ['id' => '\w+'], methods: ['GET'])]
#[Cache(maxage: 30, smaxage: 60, public: true)]
#[OA\Tag(name: 'Comments')]
#[OA\Response(response: 200, description: 'Published comments of the article', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Comment::class))))]
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
