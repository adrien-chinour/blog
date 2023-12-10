![GitHub repo size](https://img.shields.io/github/repo-size/adrien-chinour/blog)
![GitHub last commit (branch)](https://img.shields.io/github/last-commit/adrien-chinour/blog/main)

# Installation

Create `.env.local` file with :

- `CONTENTFUL_ACCESS_TOKEN` : Access token to query Contentful API
- `GITHUB_USER` : User associated with project
- `GITHUB_ACCESS_TOKEN` : Access Token to query GitHub API

> Contentful is used as Blogging provider and GitHub is used as Coding provider.

> TODO

# Project Architecture

![PHP 8.2](https://img.shields.io/badge/php_8.2-brightgreen?logo=php&logoColor=white)
![Symfony 6.3](https://img.shields.io/badge/Symfony_6.3-green?logo=symfony)

## Overview

## Layers

### Domain

### Infrastructure

### Application

### UI

# Query/Command Bus

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

> An endpoint allow you to invalidate cache

# Frontend

## Components & Controller

## Turbo

Page rendering use [Turbo Stream](https://turbo.hotwired.dev/handbook/streams) to render content. This will
fetch HTML content with Javascript without re-rendering all the document.

Integration is made with [Symfony UX](https://ux.symfony.com/turbo) with
a [Stimulus Controller](https://stimulus.hotwired.dev/).

Rendering a new page will no longer trigger Javascript reloading. If you need to trigger Javascript on every page you
will need to listen on Turbo Event :

> `turbo:render` fires after Turbo renders the page. This event fires twice during an application visit to a cached
> location: once after rendering the cached version, and again after rendering the fresh version.

Or, with an abstraction you can use `onPageLoaded` (example in `highlight.js`):

```js
import onPageLoaded from "./loader";

onPageLoaded(() => {
    hljs.highlightAll();
});
```

> This function listen on 2 events : `turbo:render` and `DOMContentLoaded`.

# Testing

> TODO : Unit test will be made using Pest.

# Analytics

# Observability

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

# CI/CD


