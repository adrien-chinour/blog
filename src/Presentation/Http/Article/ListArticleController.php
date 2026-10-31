<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticleList\GetArticleListQuery;
use App\Domain\Blogging\BlogArticle;
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
#[Route(path: '/articles', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
#[OA\Tag(name: 'Articles')]
#[OA\Response(response: 200, description: 'Latest published articles, most recent first', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: BlogArticle::class))))]
final class ListArticleController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(#[MapQueryParameter(name: 'limit')] int $limit = 10): JsonResponse
    {
        return $this->json($this->handle(new GetArticleListQuery($limit)));
    }
}
