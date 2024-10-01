<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use App\Application\Query\SearchArticle\SearchArticleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/search/articles', name: 'search_articles', methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 3600, public: true)]
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
