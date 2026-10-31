<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Application\Query\GetPreviewArticle\GetPreviewArticleQuery;
use App\Domain\Blogging\BlogArticle;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles/{id}', requirements: ['id' => '\w+'], methods: ['GET'], priority: 2)]
#[OA\Tag(name: 'Articles')]
#[OA\Response(response: 200, description: 'Article identified by its Contentful entry id. `published=false` returns the preview (draft) version and disables caching', content: new OA\JsonContent(ref: new Model(type: BlogArticle::class)))]
#[OA\Response(response: 404, description: 'Not found')]
final class GetArticleController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $id, #[MapQueryParameter('published')] bool $published = true): JsonResponse
    {
        if (null === ($article = $this->handle($published ? new GetArticleQuery($id) : new GetPreviewArticleQuery($id)))) {
            throw $this->createNotFoundException();
        }

        $response = $this->json($article);
        if ($published) {
            $response->setCache(['max_age' => 60, 's_maxage' => 120, 'public' => true]);
        }

        return $response;
    }
}
