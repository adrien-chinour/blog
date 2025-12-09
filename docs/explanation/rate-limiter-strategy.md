# Rate Limiter Strategy

This document explains the rate limiting strategy and rationale.

## Overview

The application uses Symfony's RateLimiter component with a **token-based system** rather than a simple request counter.

## Strategy Rationale

This strategy is designed to prevent random discovery bots and brute-force attacks:

- **Normal requests**: 1 token each (1000 requests/hour allowed)
- **404 errors**: 5 tokens each (~200 failed requests/hour)
- **Authentication failures**: 100 tokens each (only 10 failed attempts/hour)

This makes it extremely expensive for bots to crawl non-existent pages or attempt unauthorized access.

## Token Consumption

Different actions consume different amounts of tokens:

| Action       | Tokens Consumed | Rationale |
|--------------|-----------------|-----------|
| Any request  | 1 token         | Normal operation |
| 404 response | 5 tokens        | Discourage random URL discovery |
| 400 response | 10 tokens       | Discourage malformed requests |
| 403 response | 10 tokens       | Discourage unauthorized access attempts |
| 405 response | 10 tokens       | Discourage method probing |
| 401 response | 100 tokens       | Strongly discourage brute-force attacks |

## Implementation

Token consumption is implemented in `App\Infrastructure\Symfony\EventListener\RateLimiterEventListener`, which listens to:
- `RequestEvent`: Initial request handling
- `ResponseEvent`: Response status code evaluation

## Configuration

The rate limiter is configured with:
- **Policy**: Sliding window
- **Limit**: 1000 tokens
- **Interval**: 60 minutes

See [Security Reference](../reference/security.md) for configuration details.

