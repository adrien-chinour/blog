<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Query\GetArticle\GetArticleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/preview/{identifier}', name: 'article_preview', methods: ['GET'])]
#[Cache(public: false)]
class ArticlePreviewController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $identifier): Response
    {
        if (null === ($article = $this->handle(new GetArticleQuery($identifier, true)))) {
            throw $this->createNotFoundException();
        }

        return $this->render('pages/article_view.html.twig', ['article' => $article]);
    }
}
