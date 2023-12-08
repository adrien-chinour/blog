<?php

declare(strict_types=1);

namespace App\Presentation\Api\Comment;

use App\Application\Query\GetArticleComment\GetArticleCommentsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/article/{identifier}/comments', name: 'article_comments', methods: ['GET'])]
#[Cache(maxage: 10, public: true)]
final class ListCommentController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $identifier): JsonResponse
    {
        return $this->json(
            $this->handle(new GetArticleCommentsQuery($identifier))
        );
    }
}
