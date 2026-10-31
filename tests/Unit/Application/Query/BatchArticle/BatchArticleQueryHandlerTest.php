<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Query\BatchArticle;

use App\Application\Query\BatchArticle\BatchArticleQuery;
use App\Application\Query\BatchArticle\BatchArticleQueryHandler;
use App\Application\Query\GetArticle\GetArticleQuery;
use App\Tests\Factory\ArticleFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class BatchArticleQueryHandlerTest extends TestCase
{
    public function testUnresolvedIdentifiersAreSkipped(): void
    {
        $first = ArticleFactory::create(id: 'first');
        $third = ArticleFactory::create(id: 'third');

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function (GetArticleQuery $query) use ($first, $third): Envelope {
                $result = match ($query->identifier) {
                    'first' => $first,
                    'third' => $third,
                    default => null,
                };

                return (new Envelope($query))->with(new HandledStamp($result, 'handler'));
            });

        $articles = (new BatchArticleQueryHandler($bus))(new BatchArticleQuery(['first', 'unknown', 'third']));

        $this->assertSame([$first, $third], $articles);
    }

    public function testEmptyIdentifiersReturnsEmptyList(): void
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->never())->method('dispatch');

        $this->assertSame([], (new BatchArticleQueryHandler($bus))(new BatchArticleQuery([])));
    }
}
