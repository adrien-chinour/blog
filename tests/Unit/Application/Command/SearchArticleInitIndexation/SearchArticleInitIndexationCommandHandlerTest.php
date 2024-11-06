<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\SearchArticleInitIndexation;

use App\Application\Command\SearchArticleInitIndexation\SearchArticleInitIndexationCommand;
use App\Application\Command\SearchArticleInitIndexation\SearchArticleInitIndexationCommandHandler;
use App\Domain\Blogging\BlogArticleRepository;
use App\Tests\Factory\ArticleFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

final class SearchArticleInitIndexationCommandHandlerTest extends TestCase
{
    private BlogArticleRepository&MockObject $articleRepository;

    private MessageBusInterface&MockObject $bus;

    private SearchArticleInitIndexationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->articleRepository = $this->createMock(BlogArticleRepository::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->handler = new SearchArticleInitIndexationCommandHandler(
            $this->articleRepository,
            $this->bus,
        );
    }

    public function testBusIsNeverCallOnEmptyArticleList(): void
    {
        $this->articleRepository->expects($this->once())
            ->method('getList')
            ->willReturn([]);

        $this->bus->expects($this->never())
            ->method('dispatch');

        ($this->handler)(new SearchArticleInitIndexationCommand(true));
    }

    public function testBusIsCallOnEachArticle(): void
    {
        $this->articleRepository->expects($this->once())
            ->method('getList')
            ->willReturn(ArticleFactory::createMany(5));

        $this->bus->expects($this->exactly(5))
            ->method('dispatch');

        ($this->handler)(new SearchArticleInitIndexationCommand(true));
    }
}
