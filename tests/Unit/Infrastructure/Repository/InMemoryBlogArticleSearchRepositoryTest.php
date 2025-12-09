<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Infrastructure\Repository\InMemoryBlogArticleSearchRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class InMemoryBlogArticleSearchRepositoryTest extends TestCase
{
    private InMemoryBlogArticleSearchRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryBlogArticleSearchRepository();
    }

    public function testIndexAddsArticleToRepository(): void
    {
        $article = $this->createArticle('article-1', 'Test Article', 'Test content', 'Test description');

        $this->repository->index($article);

        $results = $this->repository->search('Test');
        $this->assertContains('article-1', $results);
    }

    public function testSearchFindsArticleByTitle(): void
    {
        $article = $this->createArticle('article-1', 'Symfony Framework', 'Content', 'Description');
        $this->repository->index($article);

        $results = $this->repository->search('Symfony');

        $this->assertContains('article-1', $results);
    }

    public function testSearchFindsArticleByContent(): void
    {
        $article = $this->createArticle('article-1', 'Title', 'This is about PHP programming', 'Description');
        $this->repository->index($article);

        $results = $this->repository->search('PHP');

        $this->assertContains('article-1', $results);
    }

    public function testSearchFindsArticleByDescription(): void
    {
        $article = $this->createArticle('article-1', 'Title', 'Content', 'Learn about Symfony framework');
        $this->repository->index($article);

        $results = $this->repository->search('Symfony');

        $this->assertContains('article-1', $results);
    }

    public function testSearchIsCaseInsensitive(): void
    {
        $article = $this->createArticle('article-1', 'Symfony Framework', 'Content', 'Description');
        $this->repository->index($article);

        $results = $this->repository->search('symfony');

        $this->assertContains('article-1', $results);
    }

    public function testSearchReturnsEmptyArrayWhenNoMatch(): void
    {
        $article = $this->createArticle('article-1', 'Test Article', 'Content', 'Description');
        $this->repository->index($article);

        $results = $this->repository->search('NonExistent');

        $this->assertEmpty($results);
    }

    public function testSearchReturnsMultipleResults(): void
    {
        $article1 = $this->createArticle('article-1', 'PHP Tutorial', 'Content', 'Description');
        $article2 = $this->createArticle('article-2', 'PHP Advanced', 'Content', 'Description');
        $this->repository->index($article1);
        $this->repository->index($article2);

        $results = $this->repository->search('PHP');

        $this->assertCount(2, $results);
        $this->assertContains('article-1', $results);
        $this->assertContains('article-2', $results);
    }

    public function testSearchReturnsOnlyArticleIds(): void
    {
        $article = $this->createArticle('article-1', 'Test', 'Content', 'Description');
        $this->repository->index($article);

        $results = $this->repository->search('Test');

        $this->assertIsArray($results);
        $this->assertContainsOnly('string', $results);
    }

    public function testIndexOverwritesExistingArticle(): void
    {
        $article1 = $this->createArticle('article-1', 'Old Title', 'Content', 'Description');
        $article2 = $this->createArticle('article-1', 'New Title', 'Content', 'Description');

        $this->repository->index($article1);
        $this->repository->index($article2);

        $results = $this->repository->search('New Title');
        $this->assertContains('article-1', $results);

        $oldResults = $this->repository->search('Old Title');
        $this->assertNotContains('article-1', $oldResults);
    }

    private function createArticle(string $id, string $title, string $content, string $description): BlogArticle
    {
        return new BlogArticle(
            id: $id,
            title: $title,
            description: $description,
            content: $content,
            imageUrl: 'https://example.com/image.jpg',
            slug: 'test-article',
            publicationDate: new DateTimeImmutable()
        );
    }
}

