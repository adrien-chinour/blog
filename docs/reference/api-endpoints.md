# API Endpoints Reference

The API reference is **generated from the code** with [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle) and must not be edited by hand.

- **Interactive reference (Swagger UI)**: [api.html](../api.html ':ignore')
- **OpenAPI 3 specification**: [`reference/openapi.json`](openapi.json ':ignore')
- **Local development**: `http://localhost:8080/api/doc` (Swagger UI) and `http://localhost:8080/api/doc.json`

## Regenerating the Specification

```bash
make doc-api
# or
docker compose run --rm php composer docs:api
```

The CI regenerates the specification and fails when `docs/reference/openapi.json` is out of date, so any route, parameter or payload change must be committed together with the regenerated file.

## Where the Documentation Comes From

| Element | Source |
|---------|--------|
| Paths, methods, path parameters | `#[Route]` attributes on controllers |
| Query parameters | `#[MapQueryParameter]` controller arguments |
| Request bodies | `#[MapRequestPayload]` DTOs (e.g. `PostCommentDto`) |
| Responses | `#[OA\Response]` attributes on controllers, schemas derived from Domain models |

The OpenAPI attributes come from a dev dependency, so the symbols are whitelisted in `composer-require-checker.json`. Add any new `OA\*` attribute there.
| Authentication | `#[IsGranted]` + the `Bearer` security scheme |

## Conventions

### Authentication

Protected endpoints require a Bearer token (`Authorization: Bearer {ADMIN_TOKEN}`). See the [Security Reference](security.md).

### Rate Limiting

Every response carries `X-RateLimit-Limit` and `X-RateLimit-Remaining`. See the [Rate Limiter Strategy](../explanation/rate-limiter-strategy.md).

### Errors

Errors are JSON objects with an `error` message. In `dev` and `test` a `debug` object (exception class, code, file, line) is added:

```json
{
  "error": "Not Found",
  "debug": { "class": "...", "code": 0, "file": "...", "line": 42 }
}
```

### Caching

Public `GET` endpoints send `Cache-Control` headers (`max-age`, `s-maxage`, `public`), see the `#[Cache]` attribute on each controller.

## See Also

- [How to Use Bruno API Collection](../how-to-guides/using-bruno-api-collection.md)
- [Security Reference](security.md)
