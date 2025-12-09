# API Endpoints Reference

Complete reference of all available API endpoints.

## Base URL

- **Development**: `http://localhost:8080`
- **Production**: Configure in your environment

## Articles

### List Articles

```
GET /articles
```

Returns a list of all articles.

### Get Article by ID

```
GET /articles/{id}
```

Returns a specific article by its ID.

### Get Article by Slug

```
GET /articles/slug/{slug}
```

Returns a specific article by its slug.

### Get Article Recommendations

```
GET /articles/{id}/recommendations
```

Returns recommended articles for a given article.

### Search Articles

```
GET /articles/search?q={query}
```

Searches articles using Meilisearch.

**Query Parameters:**
- `q` (required): Search query string

## Comments

### Get Article Comments

```
GET /articles/{id}/comments
```

Returns all comments for a specific article.

### Post Comment

```
POST /articles/{id}/comments
```

Creates a new comment for an article.

**Request Body:**
```json
{
  "author": "John Doe",
  "content": "Great article!"
}
```

## Projects

### List Projects

```
GET /projects
```

Returns a list of all projects.

## Pages

### Get Page

```
GET /pages?path={page_path}
```

Returns a static page by its path.

**Query Parameters:**
- `path` (required): Page path

## Features

### List Features

```
GET /features
```

Returns all feature flags.

## Cache

### Invalidate Cache

```
POST /cache/invalidation?tag[]={tag}
```

Invalidates cache entries by tag. Requires authentication.

**Query Parameters:**
- `tag[]` (required): One or more cache tags to invalidate

**Authentication:** Bearer token required

## Response Format

All endpoints return JSON responses. Error responses follow this format:

```json
{
  "error": "Error message",
  "code": 400
}
```

## Rate Limiting

All endpoints are subject to rate limiting. See response headers:
- `X-RateLimit-Limit`: Total available tokens
- `X-RateLimit-Remaining`: Remaining tokens

## See Also

- [How to Use Bruno API Collection](../how-to-guides/using-bruno-api-collection.md)
- [Security Reference](../reference/security.md)

