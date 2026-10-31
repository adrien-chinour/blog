<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticleByFilter\GetArticleByFilterQuery;
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
#[Route(path: '/articles/{slug}', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
#[OA\Tag(name: 'Articles')]
#[OA\Response(response: 200, description: 'Published article identified by its slug', content: new OA\JsonContent(ref: new Model(type: BlogArticle::class)))]
#[OA\Response(response: 404, description: 'Not found')]
final class GetArticleBySlugController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $slug): JsonResponse
    {
        if (null === ($article = $this->handle(new GetArticleByFilterQuery(['slug' => $slug])))) {
            throw $this->createNotFoundException();
        }

        return $this->json($article);
    }
}
