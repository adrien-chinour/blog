<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Repository;

use App\Domain\Blogging\BlogArticle;
use App\Domain\Blogging\BlogArticleRepository;
use App\Infrastructure\Contentful\Http\ContentfulApiClient;
use App\Infrastructure\Contentful\Model\ContentType\BlogPage;
use App\Infrastructure\Contentful\Model\ContentType\BlogPageCollection;
use App\Infrastructure\Contentful\Model\Factory\BlogArticleFactory;
use App\Infrastructure\GraphQL\GraphQLQueryBuilder;
use Symfony\Component\Serializer\SerializerInterface;

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

    public function getById(string $identifier, bool $preview = false): ?BlogArticle
    {
        /** @var BlogPage $data */
        $data = $this->query(BlogPage::class, ['id' => $identifier, 'preview' => $preview]);

        return $this->blogArticleFactory->fromBlogPage($data);
    }

    public function getOneBy(array $filters): ?BlogArticle
    {
        /** @var BlogPageCollection $data */
        $data = $this->query(BlogPageCollection::class, ['where' => $filters, 'limit' => 1]);

        return isset($data->items[0]) ? $this->blogArticleFactory->fromBlogPage($data->items[0]) : null;
    }

    public function getList(?int $limit = null): array
    {
        /** @var BlogPageCollection $data */
        $data = $this->query(BlogPageCollection::class, ['limit' => $limit ?? 100]);

        return $this->blogArticleFactory->fromBlogPageCollection($data);
    }
}
