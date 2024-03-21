<?php

declare(strict_types=1);

namespace App\Presentation\Api\Comment;

use App\Presentation\Api\Comment\Dto\CommentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/article/{identifier}/comments', name: 'article_comments_create', methods: ['POST'])]
final class CreateCommentController extends AbstractController
{
    public function __invoke(#[MapRequestPayload] CommentDto $dto, string $identifier): JsonResponse
    {
        return new JsonResponse();
    }
}
