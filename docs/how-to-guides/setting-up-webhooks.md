# How to Set Up Webhooks

This guide explains how to configure Contentful webhooks for automatic content updates.

## Overview

The application receives webhooks from Contentful when content is published or unpublished. This triggers automatic cache invalidation and search index updates.

## Contentful Webhook Configuration

### Step 1: Get Webhook Secret

The webhook secret is used to validate incoming webhook requests. Set it in your `.env.local`:

```env
CONTENTFUL_WEBHOOK_SECRET=your-secure-secret
```

### Step 2: Configure Webhook in Contentful

1. Log in to Contentful
2. Navigate to Settings → Webhooks
3. Click "Add webhook"
4. Configure:
   - **Name**: Blog API Webhook
   - **URL**: `https://your-domain.com/webhook/contentful`
   - **Content type**: Select `blogPage`
   - **Events**: 
     - ✅ Entry publish
     - ✅ Entry unpublish
   - **Secret**: Use the same value as `CONTENTFUL_WEBHOOK_SECRET`

### Step 3: Test Webhook

Contentful provides a test feature. Use it to verify the webhook is working.

## Webhook Endpoint

The webhook endpoint is automatically registered at:

```
POST /webhook/contentful
```

## Security

The webhook implementation includes:

- **Secret Validation**: Validates `x-contentful-secret` header
- **Timestamp Validation**: Rejects requests older than 60 seconds
- **Required Headers**: Validates presence of Contentful-specific headers

## Supported Events

### Article Published

When a `blogPage` entry is published:
- Triggers `ArticlePublishedEvent`
- Invalidates article cache
- Indexes article in Meilisearch

### Article Unpublished

When a `blogPage` entry is unpublished:
- Triggers `ArticleUnpublishedEvent`
- Invalidates article cache
- Removes article from search index

## Testing Webhooks Locally

For local development, use a tool like [ngrok](https://ngrok.com/) to expose your local server:

```bash
ngrok http 8080
```

Then use the ngrok URL in Contentful webhook configuration.

## Monitoring

Webhook events are logged. Check logs:

```bash
make logs
```

Look for:
- Webhook validation errors
- Event processing messages
- Cache invalidation confirmations

## Troubleshooting

### Webhook Rejected

If webhooks are being rejected:

1. Verify `CONTENTFUL_WEBHOOK_SECRET` matches Contentful configuration
2. Check timestamp - webhooks expire after 60 seconds
3. Verify required headers are present

### Events Not Processing

1. Check application logs
2. Verify content type is `blogPage`
3. Ensure event bus is configured correctly

## See Also

- [Webhook Reference](../reference/webhooks.md)
- [Cache Invalidation](../how-to-guides/invalidating-cache.md)

