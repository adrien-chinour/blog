# CQRS Pattern

This document explains the Command Query Responsibility Segregation (CQRS) pattern used in this project.

## What is CQRS?

CQRS (Command Query Responsibility Segregation) is an architectural pattern that separates read and write operations. In this project, it's implemented using [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html).

## Why CQRS?

CQRS provides several benefits:

- **Clear Separation**: Read and write operations are clearly separated
- **Scalability**: Read and write operations can be scaled independently
- **Flexibility**: Different optimizations can be applied to reads and writes
- **Testability**: Commands and queries can be tested independently

## Implementation

### Commands (Write Operations)

Commands represent actions that change the system state. They:
- Are handled by Command Handlers
- Don't return data (void return type)
- May trigger domain events

### Queries (Read Operations)

Queries represent requests for data. They:
- Are handled by Query Handlers
- Return data
- Can be cached for performance

## Current Implementation

Currently, both Queries and Commands use the default `sync` transport for synchronous processing.

> **Future Improvement**: Configure asynchronous transport for Commands to improve performance and scalability.

## See Also

- [Query/Command Bus Reference](../reference/query-command-bus.md)

