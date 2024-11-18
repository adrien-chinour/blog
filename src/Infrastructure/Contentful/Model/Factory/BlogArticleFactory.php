<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model\Factory;

use App\Domain\Blogging\BlogArticle;
use App\Infrastructure\Contentful\Model\ContentType\BlogPage;
use App\Infrastructure\Contentful\Model\ContentType\BlogPageCollection;
use App\Infrastructure\Contentful\Model\ContentTypes;
use App\Infrastructure\ContentParser\ContentParserInterface;
use DateTimeImmutable;

final readonly class BlogArticleFactory
{
    public function __construct(
        private ContentParserInterface $contentParser,
        private BlogTagFactory         $blogTagFactory,
    ) {}

    /**
     * Convert BlogPage to BlogArticle
     *
     * @param BlogPage $blogPage
     * @return BlogArticle
     */
    public function fromBlogPage(BlogPage $blogPage): BlogArticle
    {
        return new BlogArticle(
            id: $blogPage->sys->id,
            title: $blogPage->title,
            description: $blogPage->description,
            content: $this->contentParser->parse($blogPage->content),
            imageUrl: $blogPage->image->url,
            slug: $blogPage->slug,
            publicationDate: $blogPage->sys->firstPublishedAt ?? new DateTimeImmutable(),
            tags: $this->blogTagFactory->fromCategoryCollection($blogPage->categoriesCollection),
            recommendations: $blogPage->recommendationsCollection->getResourceIds(ContentTypes::BlogPage),
            imageDescription: is_string($blogPage->image->description) ? str_replace('<a ', '<a target="_blank" ', $blogPage->image->description) : null,
        );
    }

    /**
     * Convert all BlogPage items from BlogPageCollection to BlogArticle list
     *
     * @param BlogPageCollection $collection
     * @return BlogArticle[]
     */
    public function fromBlogPageCollection(BlogPageCollection $collection): array
    {
        return array_values(
            array_filter(
                array_map(fn (BlogPage $blogPage) => $this->fromBlogPage($blogPage), $collection->items)
            )
        );
    }
}
