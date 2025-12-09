# Search Reference

Complete reference for Meilisearch integration and search functionality.

## Overview

The application uses Meilisearch for full-text search of blog articles.

## Configuration

### Environment Variables

```env
MEILISEARCH_HOST=http://search:7700
MEILISEARCH_TOKEN=your-master-key
```

### Client Factory

The Meilisearch client is created by `MeilisearchClientFactory`:

```php
$client = new Client($meilisearchHost, $meilisearchToken);
```

## Index Structure

Articles are indexed in the `articles` index with the following fields:

- `id`: Article ID (string)
- `slug`: Article slug (string)
- `title`: Article title (string)
- `description`: Article description (string)
- `content`: Full article content (string)
- `tags`: Array of tag names (array)

## Search API

### Endpoint

```
GET /articles/search?q={query}
```

### Query Parameters

- `q` (required): Search query string

### Example

```bash
curl "http://localhost:8080/articles/search?q=symfony"
```

### Response

Returns an array of article IDs matching the search query.

## Indexing

### Automatic Indexing

Articles are automatically indexed when:
- Published via Contentful webhook
- `ArticlePublishedEvent` is dispatched

### Manual Indexing

Use the console command:

```bash
make console c='app:index-articles'
```

### Repository Methods

The `MeilisearchBlogArticleSearchRepository` provides:

- `index(BlogArticle $article)`: Index a single article
- `search(string $term)`: Search articles by term

## Meilisearch Features

### Typo Tolerance

Meilisearch provides typo tolerance by default.

### Ranking

Results are ranked by relevance using Meilisearch's default ranking rules.

### Filtering

Advanced filtering can be added by extending the search implementation.

## Console Commands

### Index Articles

```bash
php bin/console app:index-articles
```

Indexes all articles from Contentful into Meilisearch.

## See Also

- [How to Index Articles](../how-to-guides/indexing-articles.md)
- [Meilisearch Documentation](https://www.meilisearch.com/docs)

