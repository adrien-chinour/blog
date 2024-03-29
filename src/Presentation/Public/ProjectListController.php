<?php

declare(strict_types=1);

namespace App\Presentation\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/projets/', name: 'project_list', methods: ['GET'])]
#[Cache(maxage: 3600, public: true)]
final class ProjectListController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('pages/project_list.html.twig');
    }
}
