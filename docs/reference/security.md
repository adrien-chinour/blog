# Security Reference

Complete reference for security configuration and features.

## Rate Limiter

The application uses Symfony's RateLimiter component with a sliding window policy.

### Configuration

```php
// config/packages/framework.php

$framework->rateLimiter()
    ->limiter('public')
    ->policy('sliding_window')
    ->limit(1000)
    ->interval('60 minutes')
;
```

### Token-Based Consumption

Different actions consume different amounts of tokens:

| Action       | Tokens Consumed |
|--------------|-----------------|
| Any request  | 1 token         |
| 404 response | 5 tokens        |
| 400 response | 10 tokens       |
| 403 response | 10 tokens       |
| 405 response | 10 tokens       |
| 401 response | 100 tokens      |

### Response Headers

- **`X-RateLimit-Limit`**: Total available tokens (1000)
- **`X-RateLimit-Remaining`**: Remaining tokens

### Client identification

Limits are applied per client IP (`Request::getClientIp()`). Authenticated administrators are exempted.

Behind a proxy (Cloudflare, load balancer...) the proxy must be declared in the `TRUSTED_PROXIES` environment variable (comma separated IPs/CIDR, or `PRIVATE_SUBNETS` / `REMOTE_ADDR`). Only then are the forwarded headers (`X-Forwarded-For`, `Cf-Connecting-Ip`) trusted; otherwise a client could spoof them to escape the limiter.

### Storage

Rate limit data is stored using Symfony's default cache (filesystem). Resets on application deployment.

## Authentication

### Token-Based Authentication

Certain routes require the `ROLE_ADMIN` role. Authentication uses Bearer tokens.

### Implementation

- Authenticator: `Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator`
- Handler: `App\Infrastructure\Symfony\Security\AccessTokenHandler`
- Provider: In-memory provider with default admin user

### Usage

```bash
curl -X POST \
  -H "Authorization: Bearer {your-token}" \
  "https://{host}/cache/invalidation?tag[]=article"
```

## Security Features

- **Rate Limiting on Authentication**: After 3 failed attempts, IP banned for 1 hour
- **Token-based Authentication**: Bearer tokens for API authentication
- **Role-based Access Control**: Routes protected with role requirements

## Configuration

Security is configured in `config/packages/security.php`.

