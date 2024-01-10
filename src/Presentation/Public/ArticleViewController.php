<?php

declare(strict_types=1);

namespace App\Presentation\Public;

use App\Application\Query\GetArticleByFilter\GetArticleByFilterQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/article/{slug}.html', name: 'article_view', methods: ['GET'])]
#[Cache(maxage: 3600, smaxage: 86400, public: true)]
final class ArticleViewController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $slug): Response
    {
        if (null === ($article = $this->handle(new GetArticleByFilterQuery(['slug' => $slug])))) {
            throw $this->createNotFoundException();
        }

        return $this->render('pages/article_view.html.twig', ['article' => $article]);
    }
}
