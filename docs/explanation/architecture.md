# Architecture Overview

This document explains the overall architecture of the blog API gateway project.

## Project Purpose

This project is an **API gateway** designed for a Tech Blog system, connecting external systems (such as a CMS) to the blog's front end. It enables seamless integration and management of core blog components like articles, projects, and comments, while also offering advanced features like a Feature Flag system to toggle specific functionalities.

## Key Features

- **Database-less Architecture**: Unlike typical APIs, this gateway functions without a dedicated database, using simple objects to represent domain models
- **CQRS Pattern**: Implements Command Query Responsibility Segregation for clear separation of read and write operations
- **Feature Flags**: Dynamic feature toggling system
- **Caching Strategy**: Intelligent query caching with tag-based invalidation
- **Rate Limiting**: Advanced rate limiting with token-based consumption
- **Security**: Role-based access control with token authentication

## Technology Stack

- **Framework**: Symfony 7.4
- **PHP Version**: 8.4+
- **Architecture Pattern**: CQRS with layered architecture
- **Messaging**: Symfony Messenger
- **Caching**: Symfony Cache component
- **Search**: Meilisearch integration
- **Observability**: OpenTelemetry integration
- **Error Tracking**: Sentry integration

## See Also

- [System Layering](./system-layering.md)
- [CQRS Pattern](./cqrs-pattern.md)

