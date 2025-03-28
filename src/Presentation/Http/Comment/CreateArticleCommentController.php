<?php

declare(strict_types=1);

namespace App\Presentation\Http\Comment;

use App\Application\Command\PostArticleComment\PostArticleCommentCommand;
use App\Presentation\Http\Comment\Dto\PostCommentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/comments', methods: ['POST'])]
final class CreateArticleCommentController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public function __invoke(#[MapRequestPayload('json')] PostCommentDto $commentDto): JsonResponse
    {
        $this->messageBus->dispatch(new PostArticleCommentCommand(
            $commentDto->articleId,
            $commentDto->username,
            $commentDto->message
        ));

        return $this->json(data: null, status: Response::HTTP_CREATED);
    }
}
