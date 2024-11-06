<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\SearchArticleIndexation;

use App\Application\Command\SearchArticleIndexation\SearchArticleIndexationCommand;
use App\Application\Command\SearchArticleIndexation\SearchArticleIndexationCommandHandler;
use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Blogging\BlogArticleSearchRepository;
use App\Domain\Blogging\Exception\BlogArticleIndexationFailedException;
use App\Tests\Factory\ArticleFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SearchArticleIndexationCommandHandlerTest extends TestCase
{
    private BlogArticleRepository&MockObject $articleRepository;

    private BlogArticleSearchRepository&MockObject $searchArticleRepository;

    private SearchArticleIndexationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->articleRepository = $this->createMock(BlogArticleRepository::class);
        $this->searchArticleRepository = $this->createMock(BlogArticleSearchRepository::class);

        $this->handler = new SearchArticleIndexationCommandHandler($this->articleRepository, $this->searchArticleRepository);
    }

    public function testIndexationWillReturnEarlyOnUnknownArticle(): void
    {
        $this->articleRepository
            ->expects($this->once())
            ->method('getById')
            ->with('unknown')
            ->willReturn(null);

        $this->searchArticleRepository
            ->expects($this->never())
            ->method('index');

        ($this->handler)(new SearchArticleIndexationCommand('unknown'));
    }

    public function testIndexationWillReturnWithoutExceptionOnIndexationException(): void
    {
        $article = ArticleFactory::create();

        $this->articleRepository
            ->expects($this->once())
            ->method('getById')
            ->with($article->id)
            ->willReturn($article);

        $this->searchArticleRepository
            ->expects($this->once())
            ->method('index')
            ->with($article)
            ->willThrowException(new BlogArticleIndexationFailedException());

        ($this->handler)(new SearchArticleIndexationCommand($article->id));
    }

    public function testIndexationWillProceedOnValidArticle(): void
    {
        $article = ArticleFactory::create();

        $this->articleRepository
            ->expects($this->once())
            ->method('getById')
            ->with($article->id)
            ->willReturn($article);

        $this->searchArticleRepository
            ->expects($this->once())
            ->method('index')
            ->with($article);

        ($this->handler)(new SearchArticleIndexationCommand($article->id));
    }
}
