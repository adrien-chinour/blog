![GitHub repo size](https://img.shields.io/github/repo-size/adrien-chinour/blog)
![GitHub last commit (branch)](https://img.shields.io/github/last-commit/adrien-chinour/blog/main)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=bugs)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_blog&metric=coverage)](https://sonarcloud.io/summary/new_code?id=adrien-chinour_blog)

# Installation 🔧

> Installation requires `make` on your machine as well as `docker` (with a version that includes Compose).

```sh
# Create .env.local file
cp .env .env.local

# Put values on missing environnements variables (or use InMemory repository)
nano .env.local

# Build application container and install dependencies (composer)
make install
```

# Project Architecture 🏗️

![PHP 8.3](https://img.shields.io/badge/php_8.3-brightgreen?logo=php&logoColor=white)
![Symfony 7.1](https://img.shields.io/badge/Symfony_7.1-brightgreen?logo=symfony)

## Overview

This project is an **API gateway** designed for a Tech Blog system, connecting external systems (such as a CMS) to the
blog's front end. It enables seamless integration and management of core blog components like articles, projects, and
comments, while also offering advanced features like a Feature Flag system to toggle specific functionalities.

Unlike typical APIs, this gateway is built to function without a dedicated database. Instead, it leverages simple
objects to represent domain models, creating a clear separation between the **Domain Layer**
and **Infrastructure Layer**. This modular architecture enhances flexibility, enabling easy adaptation to various
external data sources. For a deeper dive into the system’s layer architecture, see the chapter on **system layering and
structure**.

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

### PHP

See the [PER Coding Style](https://www.php-fig.org/per/) (an evolution of PSR-12) as well as
the [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).

> Easy Coding Standard is used to prevent coding standard deviation on PHP code. (See Quality CI)

## System layering and structure

This project deviates from the default Symfony structure, adopting a multi-layered architecture for better organization
and separation of concerns. The layers are as follows:

1. Domain Layer: Contains the core business logic, including Models and Repository Interfaces. This layer is
   framework-agnostic, focusing solely on the business rules and data definitions that drive the application.
2. Application Layer: Defines and manages application actions, implementing the CQRS (Command Query Responsibility
   Segregation) pattern to separate read and write operations for improved clarity and maintainability.
3. Presentation Layer: Responsible for handling HTTP requests and responses, acting as the main interface between the
   API and its consumers.
4. Infrastructure Layer: Manages integration with the Symfony framework and external services such as Contentful,
   GitHub, and others. This layer serves as the bridge to third-party services, ensuring external dependencies are
   decoupled from core business logic.

>

# Query/Command Bus 🚌

The Application Layer of this project follows
the [CQRS](https://en.wikipedia.org/wiki/Command_Query_Responsibility_Segregation) (Command Query Responsibility
Segregation) architecture pattern.

This pattern is implemented using [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html), with
the default `sync` transport currently handling both Queries and Commands.

> **✨ Improvement**: Configure asynchronous transport for Commands.

## Query Caching

Using `QueryCache` attribute on query allow to cache query result.

For exemple, `GetArticleQuery` is cached for 1h _(3600s)_.

```php
#[QueryCache(
    ttl: 3600,
    tags: ['article'],
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

Cache can be purged from `/cache/invalidation` with `tag[]` defined in query.

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

## Secure Routes

Certain routes are restricted to users with the `ADMIN` role. Security is configured through Symfony’s SecurityBundle,
located in `config/security.php`.

Since there is no user database, an _in-memory_ provider is used with a default admin user assigned the `ROLE_ADMIN`.
Authentication is managed via `Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator` and
`App\Infrastructure\Symfony\Security\AccessTokenHandler`.

**Usage Example: Sending a Cache Invalidation Request**

```shell
curl -H "Authorization: Bearer {{token}}" https://{host}/cache/invalidation?tag[]=article
```

> Note: After 3 failed login attempts, the IP address will be banned for 1 hour. This behavior is configured via the
> SecurityBundle’s RateLimiter component. For more details, refer to the Symfony documentation
> on [rate limiting](https://symfony.com/doc/current/security.html#limiting-login-attempts).

# CI/CD ‍🔄

All CI/CD pipeline is made with GitHub Actions. As defined in specification workflows are defined in `.github/workflows`
folder.

Defined workflows :

- `quality.yaml` defines the continuous integration workflow. It installs and validates Composer dependencies, then runs
  quality checks, including PHPStan, ECS, PHPUnit tests, and SonarCloud analysis.
