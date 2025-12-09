# How to Configure External Services

This guide explains how to configure external services (Contentful, GitHub, Strapi, Meilisearch) for the application.

## Contentful Configuration

Contentful is used as the CMS for blog articles.

### Required Variables

Add to your `.env.local`:

```env
CONTENTFUL_SPACE_ID=your-space-id
CONTENTFUL_ACCESS_TOKEN=your-access-token
```

### Getting Credentials

1. Log in to [Contentful](https://www.contentful.com/)
2. Navigate to your space
3. Go to Settings → API keys
4. Copy the Space ID and Content Delivery API access token

### Content Model

The application expects a content type called `blogPage` in Contentful.

## GitHub Configuration

GitHub is used to fetch project information.

### Required Variables

```env
GITHUB_USER=your-github-username
GITHUB_ACCESS_TOKEN=your-personal-access-token
```

### Creating a Personal Access Token

1. Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token (classic)
3. Select scopes: `public_repo` (for public repositories)
4. Copy the token

## Strapi Configuration

Strapi is used for comments, features, and pages.

### Required Variables

```env
STRAPI_HOST=https://your-strapi-instance.com
STRAPI_TOKEN=your-strapi-api-token
```

### Getting API Token

1. Log in to your Strapi admin panel
2. Go to Settings → API Tokens
3. Create a new API token with appropriate permissions
4. Copy the token

## Meilisearch Configuration

Meilisearch is used for article search functionality.

### Required Variables

```env
MEILISEARCH_HOST=http://search:7700  # For Docker Compose
MEILISEARCH_TOKEN=your-master-key
```

### Using Docker Compose

If using Docker Compose, Meilisearch is automatically configured. Set:

```env
MEILISEARCH_HOST=http://search:7700
MEILI_MASTER_KEY=your-secure-master-key
```

The `MEILI_MASTER_KEY` in `compose.yml` should match `MEILISEARCH_TOKEN` in your `.env.local`.

### Standalone Meilisearch

For standalone Meilisearch:

```env
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_TOKEN=your-master-key
```

## Using InMemory Repositories (Development)

For local development without external services, the application can use InMemory repositories. These don't require any external service configuration.

The test environment automatically uses InMemory repositories. For development, you can configure the service container to use them.

## Verifying Configuration

After configuration, test the connections:

```bash
# Test API endpoints
curl http://localhost:8080/articles
curl http://localhost:8080/projects
curl http://localhost:8080/features
```

## See Also

- [Environment Variables Reference](../reference/environment-variables.md)
- [Repository Implementations](../explanation/repository-pattern.md)

