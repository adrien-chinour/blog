<?php

declare(strict_types=1);

namespace App\Presentation\Public;

use App\Application\Query\GetArticles\GetArticlesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/rss.xml', name: 'rss_feed', methods: ['GET'])]
#[Cache(maxage: 3600, public: true)]
final class RssFeedController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(): Response
    {
        return new Response(
            $this->renderView('pages/rss_feed.xml.twig', [
                'ttl' => 3600,
                'articles' => $this->handle(new GetArticlesQuery(100)),
            ]),
            headers: [
                'Content-Type' => 'application/xml'
            ]
        );
    }
}
