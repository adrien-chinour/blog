<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticleList\GetArticleListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 120, public: true)]
final class ListArticleController extends AbstractController
{
    use HandleTrait;

    /**
     * Bounded on purpose: every distinct value creates a new cache entry and Contentful rejects large limits.
     */
    private const MAX_LIMIT = 100;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(
        #[MapQueryParameter(
            name: 'limit',
            filter: FILTER_VALIDATE_INT,
            options: ['min_range' => 1, 'max_range' => self::MAX_LIMIT],
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )]
        int $limit = 10
    ): JsonResponse {
        return $this->json($this->handle(new GetArticleListQuery($limit)));
    }
}
