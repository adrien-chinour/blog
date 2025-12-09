# How to Index Articles in Meilisearch

This guide explains how to index articles in Meilisearch for search functionality.

## Overview

Articles need to be indexed in Meilisearch before they can be searched. This is done via manual indexing using a console command.

## Manual Indexing

### Using Console Command

Index all articles manually:

```bash
make console c='app:index-articles'
# or
docker compose run --rm php bin/console app:index-articles
```

This command:
1. Fetches all articles from Contentful
2. Indexes each article in Meilisearch
3. Reports the number of articles indexed

### Indexing Specific Article

To index a specific article, you can use the application's event system or manually trigger indexing through the console command.

## Meilisearch Index Structure

Articles are indexed with the following fields:

- `id`: Article ID
- `slug`: Article slug
- `title`: Article title
- `description`: Article description
- `content`: Full article content
- `tags`: Array of tag names

## Verifying Index

### Using Meilisearch Dashboard

Access Meilisearch dashboard (if enabled):

```
http://localhost:7700
```

### Using API

```bash
# Search for articles
curl "http://localhost:7700/indexes/articles/search?q=your+query"
```

### Using Application API

```bash
# Search via application endpoint
curl "http://localhost:8080/articles/search?q=your+query"
```

## Re-indexing

If you need to re-index all articles:

```bash
# Clear Meilisearch data (if needed)
docker compose down -v
docker compose up -d search

# Re-index articles
make console c='app:index-articles'
# or
docker compose run --rm php bin/console app:index-articles
```

## Troubleshooting

### Articles Not Appearing in Search

1. Verify articles are indexed:
   ```bash
   make console c='app:index-articles'
   # or
   docker compose run --rm php bin/console app:index-articles
   ```

2. Check Meilisearch is running:
   ```bash
   docker compose ps search
   ```

3. Verify Meilisearch configuration:
   ```bash
   # Check environment variables
   grep MEILISEARCH .env.local
   ```

### Index Errors

Check application logs for indexing errors:

```bash
make logs | grep -i meilisearch
# or
docker compose logs | grep -i meilisearch
```

## See Also

- [Search Functionality Reference](../reference/search.md)
- [Meilisearch Documentation](https://www.meilisearch.com/docs)

