<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Social\Exception\FailToPublishCommentException;
use App\Infrastructure\External\Strapi\Http\StrapiApiClient;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiCommentFactory;
use App\Infrastructure\Repository\StrapiCommentRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;

final class StrapiCommentRepositoryTest extends TestCase
{
    private StrapiApiClient&MockObject $client;
    private StrapiCommentRepository $repository;

    protected function setUp(): void
    {
        $this->client = $this->createMock(StrapiApiClient::class);
        $this->repository = new StrapiCommentRepository($this->client, $this->createStub(StrapiCommentFactory::class));
    }

    public function testCreateCommentDelegatesToClient(): void
    {
        $publishedAt = new DateTimeImmutable();

        $this->client->expects($this->once())
            ->method('postComment')
            ->with('article-1', 'adrien', 'Hello', $publishedAt);

        $this->repository->createComment('article-1', 'adrien', 'Hello', $publishedAt);
    }

    public function testCreateCommentConvertsClientFailureIntoDomainException(): void
    {
        $upstream = new TransportException('Connection refused');

        $this->client->method('postComment')->willThrowException($upstream);

        try {
            $this->repository->createComment('article-1', 'adrien', 'Hello', new DateTimeImmutable());
            $this->fail('Expected FailToPublishCommentException');
        } catch (FailToPublishCommentException $exception) {
            $this->assertSame($upstream, $exception->getPrevious());
            $this->assertStringContainsString('article-1', $exception->getMessage());
        }
    }
}
