---
paths:
  - routes/web.php
---

# Routes

## Exclude PreventRequestForgery, not the deprecated ValidateCsrfToken
Laravel 13's `web` middleware group registers `Illuminate\Foundation\Http\Middleware\PreventRequestForgery`. `ValidateCsrfToken`/`VerifyCsrfToken` are deprecated *subclasses* of it kept only for BC. `Route::withoutMiddleware()` matches by exact class or by the applied middleware being a subclass of the excluded one — excluding the subclass (`ValidateCsrfToken`) does NOT exclude the actual parent middleware (`PreventRequestForgery`) that's really running. Always exclude `PreventRequestForgery::class` directly for CSRF-exempt routes.

Also: `PreventRequestForgery` always no-ops while PHPUnit is running (`runningUnitTests()`), regardless of route exclusions. A plain `$this->post(...)` test can't prove the exclusion works. Verify instead via `app('router')->gatherRouteMiddleware($route)` and assert `PreventRequestForgery::class` is absent.
