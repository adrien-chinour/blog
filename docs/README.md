![GitHub repo size](https://img.shields.io/github/repo-size/adrien-chinour/blog)
![GitHub last commit (branch)](https://img.shields.io/github/last-commit/adrien-chinour/blog/main)

# Installation 🔧

```sh
## Create .env.local file
cp .env .env.local

# Put values on missing environnements variables
nano .env.local

# Build application container and install dependencies (composer & npm)
make install

# Build assets
make npm c="run dev"

# Or with hot-reloading
make watch
```

# Roadmap 🗺️

- Add Webhook support on Contentful
- Add Comment system on Articles
- Add tag pages

- (Add more tests)

# Project Architecture 🏗️

![PHP 8.2](https://img.shields.io/badge/php_8.2-brightgreen?logo=php&logoColor=white)
![Symfony 6.3](https://img.shields.io/badge/Symfony_6.3-green?logo=symfony)

## Overview

> TODO make a schema about project architecture

## Layers

Project not use default Symfony structure but use a multi layer organisation. These layers are :

- **Domain** : contain business logic, in our case Models and Repositories Interface.
- **Infrastructure** : make link with framework (Symfony) and External services (Contentful, GitHub, etc.).
- **Application** : define actions on application, implement CQRS pattern.
- **UI** : in charge of http request/response handling.

> See [Domain-driven design](https://en.wikipedia.org/wiki/Domain-driven_design).

# Query/Command Bus 🚌

Application Layer of project use the [CQRS](https://en.wikipedia.org/wiki/Command_Query_Responsibility_Segregation)
architecture pattern.

Implementation is made with [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html) using the
default `sync` Transport for Query and Command.

> Improvement : Use an async Transport for Command.

## Query Caching

Using `CacheableQueryInterface` on query allow to cache query result.

For exemple, `GetArticleQuery` is cached for 1h _(60 * 60 = 3600)_.

```php
final readonly class GetArticleQuery implements CacheableQueryInterface
{
    public function __construct(public string $identifier, public bool $preview = false) {}

    public function getCacheKey(): string
    {
        return sprintf('article_%s', $this->identifier);
    }

    public function getCacheTtl(): int
    {
        return $this->preview ? 0 : 3600;
    }
}
```

- `getCacheKey` : key used in cache system to store query result.
- `getCacheTtl` : time-to-live for cache entry in seconds.

## Cache invalidation

```http request
GET {app_endpoint}/admin/cache-invalidation?cacheKey={cacheKey}
```

Cache can be purged from `/admin/cache-invalidation` with `cacheKey` defined in query.

> TODO : add security on /admin routes

# Frontend 🌐

## Components & Controller

## Turbo

Page rendering use [Turbo Stream](https://turbo.hotwired.dev/handbook/streams) to render content. This will
fetch HTML content with Javascript without re-rendering all the document.

Integration is made with [Symfony UX](https://ux.symfony.com/turbo) with
a [Stimulus Controller](https://stimulus.hotwired.dev/).

Rendering a new page will no longer trigger Javascript reloading. If you need to trigger Javascript on every page you
will need to listen
on [Turbo Events](https://turbo.hotwired.dev/handbook/building#observing-navigation-events) : `turbo:load`.

# Testing 🧪

> **TODO** 😱 😥

# Analytics 📊

# Observability 🔭

## Frontend observability using Grafana Faro

[Grafana Faro](https://grafana.com/oss/faro/) is an OSS Project for Frontend application Observability.

The project provide Javascript librairies to integrate all frontend metrics and logs (including Errors) directly on
Grafana.

Integration is made on `observability.js` :

```js
import {getWebInstrumentations, initializeFaro} from '@grafana/faro-web-sdk';
import {TracingInstrumentation} from '@grafana/faro-web-tracing';

if (process.env.NODE_ENV === 'production') {
    initializeFaro({
        url: 'https://faro-collector-prod-eu-west-0.grafana.net/collect/9689c3ba5a20d52b36dec6a5da24f8eb',
        app: {
            name: 'udfn.fr',
            version: '1.0.0',
            environment: 'production'
        },
        instrumentations: [
            ...getWebInstrumentations(),
            new TracingInstrumentation(),
        ],
    });
}
```

> Faro is only initialize on **production** environnement.

# CI/CD ‍🔄

All CI/CD pipeline is made with GitHub Actions. As defined in specification workflows are defined in `.github/workflows`
folder.

Two workflows is defined :

- `quality.yaml` is the continuous integration workflow. It install and validate composer dependencies, then run quality
  checks like PHPStan, ECS, Pest test suites and k6 load tests.
- `release.yaml` is the Continuous deployment workflow. Il will be trigger when quality workflow succeed on `main`
  branch. The release is basically a call to DigitalOcean Build & Deploy pipeline.
