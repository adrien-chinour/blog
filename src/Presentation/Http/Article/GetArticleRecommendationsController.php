<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticleRecommendations\GetArticleRecommendationsQuery;
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
