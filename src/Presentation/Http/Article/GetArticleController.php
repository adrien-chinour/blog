<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticle\GetArticleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles/{id}', requirements: ['id' => '\w+'], methods: ['GET'], priority: 2)]
#[Cache(maxage: 60, smaxage: 120, public: true)]
final class GetArticleController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $id, #[MapQueryParameter('published')] bool $published = true): JsonResponse
    {
        if (null === ($article = $this->handle(new GetArticleQuery($id, $published)))) {
            throw $this->createNotFoundException();
        }

        $response = $this->json($article);
        if ($published) {
            $response->setCache(['max-age' => 60, 's-maxage' => 120, 'public' => true]);
        }

        return $response;
    }
}
