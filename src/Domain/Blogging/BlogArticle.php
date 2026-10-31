<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

use DateTimeInterface;

final readonly class BlogArticle
{
    /**
     * @param list<BlogTag> $tags
     * @param list<string> $recommendations Identifiers of the recommended articles
     */
    public function __construct(
        public string             $id,
        public string             $title,
        public string             $description,
        public string             $content,
        public string             $imageUrl,
        public string             $slug,
        public DateTimeInterface $publicationDate,
        public array              $tags = [],
        public array              $recommendations = [],
        public ?string             $imageDescription = null,
    ) {}
}
