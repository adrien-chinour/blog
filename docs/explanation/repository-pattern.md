# Repository Pattern

This document explains the repository pattern implementation and how to switch between implementations.

## Overview

The application uses the Repository pattern to abstract data access. This allows switching between different implementations (real services vs in-memory) without changing business logic.

## Repository Implementations

### Production/Development Repositories

In production and development, the following repositories are used:

- **Articles**: `ContentfulBlogArticleRepository` (Contentful CMS)
- **Search**: `MeilisearchBlogArticleSearchRepository` (Meilisearch)
- **Projects**: `GithubProjectRepository` (GitHub API)
- **Comments**: `StrapiCommentRepository` (Strapi CMS)
- **Features**: `StrapiFeatureRepository` (Strapi CMS)
- **Pages**: `StrapiPageRepository` (Strapi CMS)

### Test Repositories

In the test environment, InMemory repositories are automatically used:

- `InMemoryBlogArticleRepository`
- `InMemoryBlogArticleSearchRepository`
- `InMemoryProjectRepository`
- `InMemoryCommentRepository`
- `InMemoryFeatureRepository`
- `InMemoryPageRepository`

## Configuration

Repository implementations are configured in `config/services.php`:

```php
// Production/Development aliases
$services->alias(BlogArticleRepository::class, ContentfulBlogArticleRepository::class);
$services->alias(BlogArticleSearchRepository::class, MeilisearchBlogArticleSearchRepository::class);

// Test environment override
if ($container->env() === 'test') {
    // InMemory repositories are used
}
```

## Benefits

### Testability

InMemory repositories allow:
- Fast test execution
- No external service dependencies
- Predictable test data

### Flexibility

Easy switching between:
- Real services (production)
- Mock services (testing)
- Different implementations (e.g., different CMS)

### Decoupling

Business logic (Domain layer) doesn't depend on:
- Specific external services
- Database implementations
- API clients

## Repository Interfaces

All repositories implement interfaces defined in the Domain layer:

- `BlogArticleRepository`
- `BlogArticleSearchRepository`
- `ProjectRepository`
- `CommentRepository`
- `FeatureRepository`
- `PageRepository`

## See Also

- [System Layering](./system-layering.md)
- [How to Configure External Services](../how-to-guides/configuring-external-services.md)

