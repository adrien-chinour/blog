<?php

declare(strict_types=1);

namespace App\Presentation\Http\Article;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Application\Query\GetArticleByFilter\GetArticleByFilterQuery;
use App\Application\Query\GetPreviewArticle\GetPreviewArticleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Resolves an article either by its identifier or by its slug.
 *
 * Both share the same path on purpose: two routes (/articles/{id} and /articles/{slug}) cannot be
 * told apart by a regex since a slug without dash (ex: "docker") is also a valid identifier.
 */
#[AsController]
#[Route(path: '/articles/{identifier}', requirements: ['identifier' => '[\w\-]+'], methods: ['GET'])]
final class GetArticleController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $identifier, #[MapQueryParameter('published')] bool $published = true): JsonResponse
    {
        if (!$published) {
            // Preview mode exposes unpublished content: restricted to authenticated administrators
            $this->denyAccessUnlessGranted('ROLE_ADMIN', message: 'Preview mode is restricted to administrators');
        }

        if (null === ($article = $this->findArticle($identifier, $published))) {
            throw $this->createNotFoundException();
        }

        $response = $this->json($article);
        if ($published) {
            $response->setCache(['max_age' => 60, 's_maxage' => 120, 'public' => true]);
        }

        return $response;
    }

    private function findArticle(string $identifier, bool $published): mixed
    {
        // Identifiers never contain dashes: skip the lookup by id when the value is obviously a slug
        if (!str_contains($identifier, '-')) {
            $article = $this->handle($published ? new GetArticleQuery($identifier) : new GetPreviewArticleQuery($identifier));
            if (null !== $article) {
                return $article;
            }
        }

        // Preview is only available by identifier
        if (!$published) {
            return null;
        }

        return $this->handle(new GetArticleByFilterQuery(['slug' => $identifier]));
    }
}
