<?php

declare(strict_types=1);

namespace App\Domain\Blogging;

final readonly class BlogArticle
{
    public function __construct(
        public string             $id,
        public string             $title,
        public string             $description,
        public string             $content,
        public string             $imageUrl,
        public string             $slug,
        public \DateTimeInterface $publicationDate,
        public array              $tags = [],
        public array              $recommendations = [],
        public ?string             $imageDescription = null,
    ) {}
}
