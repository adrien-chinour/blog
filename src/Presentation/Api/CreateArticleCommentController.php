<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use App\Application\Command\PostArticleComment\PostArticleCommentCommand;
use App\Presentation\Api\Dto\PostCommentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/comments', name: 'post_article_comment', methods: ['POST'])]
final class CreateArticleCommentController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public function __invoke(#[MapRequestPayload] PostCommentDto $commentDto): JsonResponse
    {
        $this->messageBus->dispatch(new PostArticleCommentCommand(
            $commentDto->articleId,
            $commentDto->username,
            $commentDto->message
        ));

        return $this->json(data: null, status: Response::HTTP_CREATED);
    }
}
