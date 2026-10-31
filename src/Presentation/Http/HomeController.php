<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', methods: ['GET'])]
#[OA\Tag(name: 'Health')]
#[OA\Response(response: 200, description: 'Service is up', content: new OA\JsonContent(properties: [new OA\Property(property: 'status', type: 'string', example: 'ok')]))]
final class HomeController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        return $this->json(['status' => 'ok']);
    }
}
