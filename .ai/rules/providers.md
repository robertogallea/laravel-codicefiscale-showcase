---
paths:
  - app/Providers/AppServiceProvider.php
---

# Providers

## Register laravel-codicefiscale's UpdatePlacesCommand ourselves for web-triggered runs
`Robertogallea\CodiceFiscale\Laravel\CodiceFiscaleServiceProvider::boot()` only registers `codice-fiscale:update-places` inside `if ($this->app->runningInConsole())`. `Application::runningInConsole()` checks `PHP_SAPI === 'cli'` — true for `php artisan ...` and for PHPUnit/Pest test runs, but false for a normal web request. `RefreshCodiceFiscaleDataController` calls `Artisan::call('codice-fiscale:update-places')` from an HTTP request, so without registering the command ourselves it throws `CommandNotFoundException` in production while working fine locally/in tests (same class of "PHP_SAPI-dependent behavior masked by the CLI test runner" trap as `PreventRequestForgery`'s `runningUnitTests()` short-circuit — see [[routes]]).

`AppServiceProvider::boot()` re-registers it unconditionally via `$this->commands([UpdatePlacesCommand::class])`. Don't remove that. To test this class of bug, boot a fresh app in a subprocess with `APP_RUNNING_IN_CONSOLE=false` (a same-process test can't force `runningInConsole()` false) — see the last test in `tests/Feature/RefreshCodiceFiscaleDataTest.php`.
