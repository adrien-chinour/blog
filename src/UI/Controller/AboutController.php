<?php

declare(strict_types=1);

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/a-propos/', name: 'about', methods: ['GET'])]
#[Cache(maxage: 3600, public: true)]
final class AboutController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('pages/about.html.twig');
    }
}
