<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Blogging\BlogTag;
use App\Domain\Blogging\Exception\BlogArticleIndexationFailedException;
use App\Infrastructure\Repository\MeilisearchBlogArticleSearchRepository;
use App\Tests\Factory\ArticleFactory;
use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;
use Meilisearch\Exceptions\CommunicationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MeilisearchBlogArticleSearchRepositoryTest extends TestCase
{
    private Indexes&MockObject $index;
    private MeilisearchBlogArticleSearchRepository $repository;

    protected function setUp(): void
    {
        $this->index = $this->createMock(Indexes::class);

        $client = $this->createMock(Client::class);
        $client->method('index')->with('articles')->willReturn($this->index);

        $this->repository = new MeilisearchBlogArticleSearchRepository($client);
    }

    public function testIndexSendsPlainTextContent(): void
    {
        $article = ArticleFactory::create(
            id: 'article-1',
            content: "<h1>Title</h1>\n<p>Some <span class=\"hljs-keyword\">code</span> &amp; text</p>",
            tags: [new BlogTag('tag-php', 'PHP', 'php')],
        );

        $this->index->expects($this->once())
            ->method('addDocuments')
            ->with($this->callback(function (array $documents) use ($article): bool {
                $this->assertCount(1, $documents);
                $this->assertSame('article-1', $documents[0]['id']);
                $this->assertSame('Title Some code & text', $documents[0]['content']);
                $this->assertSame($article->title, $documents[0]['title']);
                $this->assertSame(['PHP'], $documents[0]['tags']);

                return true;
            }));

        $this->repository->index($article);
    }

    public function testIndexConvertsMeilisearchFailureIntoDomainException(): void
    {
        $upstream = new CommunicationException('Meilisearch unreachable');
        $this->index->method('addDocuments')->willThrowException($upstream);

        try {
            $this->repository->index(ArticleFactory::create(id: 'article-1'));
            $this->fail('Expected BlogArticleIndexationFailedException');
        } catch (BlogArticleIndexationFailedException $exception) {
            $this->assertSame($upstream, $exception->getPrevious());
            $this->assertStringContainsString('article-1', $exception->getMessage());
        }
    }
}
