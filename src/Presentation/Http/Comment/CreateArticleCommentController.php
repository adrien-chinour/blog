<?php

declare(strict_types=1);

namespace App\Presentation\Http\Comment;

use App\Application\Command\PostArticleComment\PostArticleCommentCommand;
use App\Presentation\Http\Comment\Dto\PostCommentDto;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/comments', methods: ['POST'])]
#[OA\Tag(name: 'Comments')]
#[OA\Response(response: 201, description: 'Comment submitted for moderation')]
#[OA\Response(response: 400, description: 'Invalid comment (unknown article, length or content rules)')]
#[OA\Response(response: 422, description: 'Malformed payload')]
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
