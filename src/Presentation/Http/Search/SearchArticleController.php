<?php

declare(strict_types=1);

namespace App\Presentation\Http\Search;

use App\Application\Query\SearchArticle\SearchArticleQuery;
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
#[Route(path: '/search/articles', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
#[OA\Tag(name: 'Search')]
#[OA\Response(response: 200, description: 'Identifiers of the articles matching the full-text query (Meilisearch)', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'string', example: '4rDKoxkMBXRMGZ7ucuMg6y')))]
final class SearchArticleController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(#[MapQueryParameter('query')] string $query): JsonResponse
    {
        return $this->json($this->handle(new SearchArticleQuery($query)));
    }
}
