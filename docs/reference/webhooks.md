# Webhooks Reference

Complete reference for webhook configuration and handling.

## Endpoint

```
POST /webhook/contentful
```

## Configuration

Webhooks are configured in `config/packages/framework.php`:

```php
$framework->webhook()
    ->routing('contentful', [
        'service' => ContentfulRequestParser::class,
        'secret' => '%env(CONTENTFUL_WEBHOOK_SECRET)%',
    ]);
```

## Request Validation

The webhook validates:

1. **HTTP Method**: Must be POST
2. **Required Headers**:
   - `x-contentful-secret`: Must match `CONTENTFUL_WEBHOOK_SECRET`
   - `x-contentful-timestamp`: Must be within 60 seconds
   - `x-contentful-crn`: Content resource name
   - `x-contentful-topic`: Event topic

3. **Timestamp Validation**: Requests older than 60 seconds are rejected

## Supported Topics

### ContentManagement.Entry.publish

Triggered when a `blogPage` entry is published.

**Event**: `ArticlePublishedEvent`

**Actions**:
- Invalidates article cache
- Indexes article in Meilisearch

### ContentManagement.Entry.unpublish

Triggered when a `blogPage` entry is unpublished.

**Event**: `ArticleUnpublishedEvent`

**Actions**:
- Invalidates article cache
- Removes article from Meilisearch index

## Request Parser

The `ContentfulRequestParser` class:
- Validates request format
- Checks security headers
- Parses payload into `ContentfulRemoteEvent`

## Event Consumer

The `ContentfulEventConsumer` class:
- Listens for Contentful remote events
- Routes events based on content type
- Dispatches domain events to event bus

## Error Handling

- Invalid requests return 406 status
- Validation errors are logged
- Unknown content types are logged as warnings

## See Also

- [How to Set Up Webhooks](../how-to-guides/setting-up-webhooks.md)

