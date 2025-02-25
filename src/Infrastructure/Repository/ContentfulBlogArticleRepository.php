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
        /** @var BlogPage $data */
        $data = $this->query(BlogPage::class, ['id' => $identifier, 'preview' => !$published]);

        try {
            return $this->blogArticleFactory->fromBlogPage($data);
        } catch (Throwable) {
            return null;
        }
    }

    public function getOneBy(array $filters): ?BlogArticle
    {
        /** @var BlogPageCollection $data */
        $data = $this->query(BlogPageCollection::class, ['where' => $filters, 'limit' => 1]);

        try {
            return isset($data->items[0]) ? $this->blogArticleFactory->fromBlogPage($data->items[0]) : null;
        } catch (Throwable) {
            return null;
        }
    }

    public function getList(?int $limit = null): array
    {
        /** @var BlogPageCollection $data */
        $data = $this->query(BlogPageCollection::class, ['limit' => $limit ?? 100]);

        $articles = $this->blogArticleFactory->fromBlogPageCollection($data);
        usort(
            $articles,
            fn (BlogArticle $a, BlogArticle $b) => $a->publicationDate->getTimestamp() > $b->publicationDate->getTimestamp() ? -1 : 1
        );

        return $articles;
    }
}
