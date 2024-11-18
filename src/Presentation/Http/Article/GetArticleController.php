<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Application\Query\GetPreviewArticle\GetPreviewArticleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/articles/{id}', requirements: ['id' => '\w+'], methods: ['GET'], priority: 2)]
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
