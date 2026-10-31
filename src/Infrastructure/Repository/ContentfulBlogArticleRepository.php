<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use App\Infrastructure\Component\GraphQL\GraphQLQueryBuilder;
use App\Infrastructure\External\Contentful\Http\ContentfulApiClient;
use App\Infrastructure\External\Contentful\Model\ContentType\BlogPage;
use App\Infrastructure\External\Contentful\Model\ContentType\BlogPageCollection;
use App\Infrastructure\External\Contentful\Model\Factory\BlogArticleFactory;
use App\Infrastructure\External\Contentful\Repository\AbstractContentfulRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class ContentfulBlogArticleRepository extends AbstractContentfulRepository implements BlogArticleRepository
{
    public function __construct(
        private readonly BlogArticleFactory $blogArticleFactory,
        ContentfulApiClient                 $apiClient,
        SerializerInterface                 $serializer,
        GraphQLQueryBuilder                 $queryBuilder
    ) {
        parent::__construct($apiClient, $serializer, $queryBuilder);
    }

    public function getById(string $identifier, bool $published = true): ?BlogArticle
    {
        $data = $this->query(BlogPage::class, ['id' => $identifier, 'preview' => !$published]);
        if (!$data instanceof BlogPage) {
            return null;
        }

        try {
            return $this->blogArticleFactory->fromBlogPage($data);
        } catch (Throwable) {
            return null;
        }
    }

    public function getOneBy(array $filters): ?BlogArticle
    {
        $data = $this->query(BlogPageCollection::class, ['where' => $filters, 'limit' => 1]);
        if (!$data instanceof BlogPageCollection || !isset($data->items[0])) {
            return null;
        }

        try {
            return $this->blogArticleFactory->fromBlogPage($data->items[0]);
        } catch (Throwable) {
            return null;
        }
    }

    public function getList(?int $limit = null): array
    {
        $data = $this->query(BlogPageCollection::class, ['limit' => $limit ?? 100]);
        if (!$data instanceof BlogPageCollection) {
            return [];
        }

        $articles = $this->blogArticleFactory->fromBlogPageCollection($data);
        usort(
            $articles,
            fn (BlogArticle $a, BlogArticle $b): int => $b->publicationDate->getTimestamp() <=> $a->publicationDate->getTimestamp()
        );

        return $articles;
    }
}
