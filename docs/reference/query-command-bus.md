# Query/Command Bus Reference

Reference for the CQRS implementation using Symfony Messenger.

## Overview

The Application Layer follows the [CQRS](https://en.wikipedia.org/wiki/Command_Query_Responsibility_Segregation) pattern, implemented using [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html).

Currently, both Queries and Commands use the default `sync` transport for synchronous processing.

## Query (Read Operation)

### Define a Query

```php
final readonly class GetArticleQuery implements QueryInterface
{
    public function __construct(
        public string $slug
    ) {}
}
```

### Query Handler

```php
final readonly class GetArticleQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $repository
    ) {}

    public function __invoke(GetArticleQuery $query): Article
    {
        return $this->repository->findBySlug($query->slug);
    }
}
```

### Usage in Controller

```php
$article = $this->queryBus->dispatch(new GetArticleQuery($slug));
```

## Command (Write Operation)

### Define a Command

```php
final readonly class PostCommentCommand implements CommandInterface
{
    public function __construct(
        public string $articleId,
        public string $author,
        public string $content
    ) {}
}
```

### Command Handler

```php
final readonly class PostCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $repository
    ) {}

    public function __invoke(PostCommentCommand $command): void
    {
        $comment = new Comment(
            articleId: $command->articleId,
            author: $command->author,
            content: $command->content
        );
        $this->repository->save($comment);
    }
}
```

## Query Caching

See [Cache Reference](./cache.md) for query caching details.

