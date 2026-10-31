<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticleRecommendations\GetArticleRecommendationsQuery;
use App\Domain\Blogging\BlogArticle;
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
#[Route(path: '/articles/{id}/recommendations', requirements: ['id' => '\w+'], methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
#[OA\Tag(name: 'Articles')]
#[OA\Response(response: 200, description: 'Articles recommended for the given article', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: BlogArticle::class))))]
#[OA\Response(response: 404, description: 'Not found')]
final class GetArticleRecommendationsController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $id): JsonResponse
    {
        if (null === ($recommendations = $this->handle(new GetArticleRecommendationsQuery($id)))) {
            throw $this->createNotFoundException();
        }

        return $this->json($recommendations);
    }
}
