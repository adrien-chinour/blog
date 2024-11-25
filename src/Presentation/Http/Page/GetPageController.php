<?php

declare(strict_types=1);

namespace App\Presentation\Http\Page;

use App\Application\Query\GetPage\GetPageQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/pages', methods: ['GET'])]
#[Cache(maxage: 30, smaxage: 60, public: true)]
final class GetPageController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(#[MapQueryParameter('path')] string $path): JsonResponse
    {
        if (null === ($page = $this->handle(new GetPageQuery($path)))) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->json($page);
    }
}
