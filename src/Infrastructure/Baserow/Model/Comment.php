<?php

declare(strict_types=1);

namespace App\Infrastructure\Baserow\Model;

use App\Domain\Social\Comment as DomainComment;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class Comment
{
    public function __construct(
        #[SerializedName('article_id')]
        public string $articleId,
        public string $message,
        public string $username,
        #[SerializedName('published_at')]
        public \DateTimeImmutable $publishedAt,
        public bool $moderated,
    ) {}

    public function toDomain(): DomainComment
    {
        return new DomainComment(
            id: hash('md5', sprintf('%s/%d', $this->username, $this->publishedAt->getTimestamp())),
            username: $this->username,
            message: $this->message,
            publishedAt: $this->publishedAt,
        );
    }
}
