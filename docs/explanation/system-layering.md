# System Layering and Structure

This document explains the multi-layered architecture adopted by this project.

## Overview

This project deviates from the default Symfony structure, adopting a **multi-layered architecture** for better organization and separation of concerns.

## Layer Architecture

```
┌─────────────────────────────────────┐
│   Presentation Layer (HTTP)        │
│   - Controllers                     │
│   - Request/Response handling       │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│   Application Layer (CQRS)          │
│   - Commands (Write operations)     │
│   - Queries (Read operations)       │
│   - Event Handlers                  │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│   Domain Layer (Business Logic)     │
│   - Domain Models                   │
│   - Repository Interfaces           │
│   - Value Objects                   │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│   Infrastructure Layer              │
│   - Symfony Integration             │
│   - External Services (Contentful)  │
│   - Repository Implementations      │
└─────────────────────────────────────┘
```

## Layer Responsibilities

### Domain Layer (`src/Domain/`)

- Contains the core business logic, including Models and Repository Interfaces
- Framework-agnostic, focusing solely on business rules and data definitions
- No dependencies on external frameworks or services

### Application Layer (`src/Application/`)

- Defines and manages application actions
- Implements CQRS pattern to separate read (Queries) and write (Commands) operations
- Contains event handlers for domain events

### Presentation Layer (`src/Presentation/`)

- Handles HTTP requests and responses
- Acts as the main interface between the API and its consumers
- Contains controllers and request/response DTOs

### Infrastructure Layer (`src/Infrastructure/`)

- Manages integration with the Symfony framework
- Implements external service integrations (Contentful, GitHub, etc.)
- Contains repository implementations
- Bridges third-party services while keeping them decoupled from core business logic

## Architecture Rules

The project uses [PHPArkitect](https://github.com/phparkitect/phparkitect) to enforce architectural rules. These rules ensure:
- Proper layer dependencies (e.g., Domain layer has no dependencies on other layers)
- Correct namespace organization
- Adherence to architectural patterns

Run architecture checks:
```bash
make quality
# or
docker compose run --rm php composer run-script quality
# or
docker compose run --rm php vendor/bin/phparkitect check --config=tests/Architecture/arkitect.php
```

