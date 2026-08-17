---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Type-hint controller returns as Response|JsonResponse when both are used
`Illuminate\Http\JsonResponse` does NOT extend `Illuminate\Http\Response` — they're siblings, each extending a different Symfony base class. A controller method declared `: Response` that has any `return response()->json(...)` branch throws a `TypeError` at runtime the moment that branch executes (framework/test errors mask this until that specific branch runs — abort()/noContent() paths won't trigger it).

If a controller mixes `response()->json(...)` with `response()->noContent()` or other `Response`-returning helpers, type-hint the method `: Response|JsonResponse` (or unify on one response type). Caught in `RefreshCodiceFiscaleDataController` — production 500s on paths the test suite didn't exercise (lock-conflict 409, non-zero exit 500, exception 500). Cover every distinct return branch with a test, not just the happy path.
