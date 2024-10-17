<?php

declare(strict_types=1);

namespace App\Presentation\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class HomeController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        return $this->json(['status' => 'ok']);
    }
}
