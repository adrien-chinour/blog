![GitHub repo size](https://img.shields.io/github/repo-size/adrien-chinour/blog)
![GitHub last commit (branch)](https://img.shields.io/github/last-commit/adrien-chinour/blog/main)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=bugs)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)

# Installation 🔧

```sh
# Create .env.local file
cp .env .env.local

# Put values on missing environnements variables
nano .env.local

# Build application container and install dependencies (composer)
make install
```

# Project Architecture 🏗️

![PHP 8.3](https://img.shields.io/badge/php_8.3-brightgreen?logo=php&logoColor=white)
![Symfony 7.1](https://img.shields.io/badge/Symfony_7.1-brightgreen?logo=symfony)

## Overview

> TODO make a schema about project architecture

## Coding standards

### Git

Commit **MUST** respect [Conventional Commits specifications](https://www.conventionalcommits.org/en/v1.0.0/)

Allowed types are :

- **feat** – a new feature is introduced with the changes
- **fix** – a bug fix has occurred
- **chore** – changes that do not relate to a fix or feature and don't modify src or test files (for example updating
  dependencies)
- **refactor** – refactored code that neither fixes a bug nor adds a feature
- **docs** – updates to documentation such as a the README or other markdown files
- **style** – changes that do not affect the meaning of the code, likely related to code formatting such as white-space,
  missing semi-colons, and so on.
- **test** – including new or correcting previous tests
- **perf** – performance improvements
- **ci** – continuous integration related
- **build** – changes that affect the build system or external dependencies
- **revert** – reverts a previous commit

## Layers

Project not use default Symfony structure but use a multi layer organisation. These layers are :

- **Domain** : contain business logic, in our case Models and Repositories Interface.
- **Infrastructure** : make link with framework (Symfony) and External services (Contentful, GitHub, etc.).
- **Application** : define actions on application, implement CQRS pattern.
- **Presentation** : in charge of http request/response handling.

> See [Domain-driven design](https://en.wikipedia.org/wiki/Domain-driven_design).

# Query/Command Bus 🚌

Application Layer of project use the [CQRS](https://en.wikipedia.org/wiki/Command_Query_Responsibility_Segregation)
architecture pattern.

Implementation is made with [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html) using the
default `sync` Transport for Query and Command.

> **✨ Improvement** : Use an async Transport for Command.

## Query Caching

Using `QueryCache` attribute on query allow to cache query result.

For exemple, `GetArticleQuery` is cached for 1h _(3600s)_.

```php
#[QueryCache(
    ttl: 3600,
    tags: ['get_article', 'article'],
)]
final readonly class GetArticleQuery implements CacheableQueryInterface
{
    // ...
}
```

- ttl : cache duration
- tags : allow to invalidate multiple cache entries by tag

> To see more, cache implementation is made on `App\Infrastructure\Cache` and used by a Symfony Messenger
> middleware : `App\Infrastructure\Symfony\Messenger\Middleware\CacheMiddleware`

## Cache invalidation

Cache can be purged from `/webhook/cache-invalidation` with `tag[]` defined in query.

# Security 👮

## Rate Limiter

This app use Symfony RateLimiter component to prevent too many request. Limiter is configured like this :

```php
// framework.php

$framework->rateLimiter()
    ->limiter('public')
    ->policy('sliding_window')
    ->limit(1000)
    ->interval('60 minutes')
;
```

> RateLimiter component documentation : https://symfony.com/doc/current/rate_limiter.html

Limit is not a number of request but a number of token consumed using these rules :

| action       | consume    |
|--------------|------------|
| any request  | 1 token    |
| 404 response | 5 tokens   |
| 400 response | 10 tokens  |
| 403 response | 10 tokens  |
| 405 response | 10 tokens  |
| 401 response | 100 tokens |

Implementation of token consumption is in `App\Infrastructure\Symfony\EventListener\RateLimiterEventListener`. This
event listener listen on Symfony HttpKernel `RequestEvent` and `ResponseEvent`.

The idea behind this strategy is to prevent random discovery bot. For exemple, bots can only crawl around 165 url in 1
hour, if all crawled page are 404. Only 10, if all page need authentication.

2 headers is available on response :

- `X-RateLimit-Limit` number of available token.
- `X-RateLimit-Remaining` : number of remaining tokens

> rate limit is store using default Symfony cache (filesystem). It will be reset on every new app deployment.

## Secure routes

Accessing routes under `^/webhook` is available with authentication. Security is configured under Symfony
SecurityBundle in `config/security.php`.

There is no user database, it used _in memory_ provider with a default admin user with ROLE_ADMIN. Authentication
use `Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator`
and `App\Infrastructure\Symfony\Security\AccessTokenHandler`.

**Usage (send a cache invalidation request):**

```shell
curl -H "Authorization: Bearer {{token}}" https://www.udfn.fr/webhook/cache-invalidation?tag[]=article
```

> 3 bad login attempt will ban IP for 1 hour. (Configuration from SecurityBundle using RateLimiter component).
> See documentation : https://symfony.com/doc/current/security.html#limiting-login-attempts

# CI/CD ‍🔄

All CI/CD pipeline is made with GitHub Actions. As defined in specification workflows are defined in `.github/workflows`
folder.

Defined workflows :

- `quality.yaml` is the continuous integration workflow. It install and validate composer dependencies, then run quality
  checks like PHPStan, ECS, Pest test suites and k6 load tests.
