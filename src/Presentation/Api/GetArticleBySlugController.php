<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use App\Application\Query\GetArticleByFilter\GetArticleByFilterQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles/{slug}', name: 'get_article_by_slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
#[Cache(maxage: 60, smaxage: 3600, public: true)]
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
