<?php

declare(strict_types=1);

namespace App\Presentation\Public;

use App\Application\Query\GetArticleList\GetArticleListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
#[Cache(maxage: 3600, public: true)]
final class SitemapController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(): Response
    {
        return new Response(
            $this->renderView('pages/sitemap.xml.twig', [
                'ttl' => 3600,
                'articles' => $this->handle(new GetArticleListQuery(100)),
            ]),
            headers: [
                'Content-Type' => 'application/xml'
            ]
        );
    }
}
