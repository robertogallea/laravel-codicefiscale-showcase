<?php

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;

it('excludes CSRF/forgery protection from the scheduler endpoint', function () {
    // Laravel's PreventRequestForgery middleware short-circuits to a no-op whenever
    // PHPUnit is running, so a plain $this->post(...) assertion can't tell "properly
    // excluded" apart from "still applied but silenced by the test environment".
    // Inspect the route's resolved middleware stack directly instead, mirroring what
    // Illuminate\Routing\Router::gatherRouteMiddleware() computes for a real request.
    $route = Route::getRoutes()->getByName('internal.codice-fiscale-update');

    $resolved = app('router')->gatherRouteMiddleware($route);

    expect($resolved)->not->toContain(PreventRequestForgery::class);
});

it('rejects a codice fiscale data refresh without the scheduler token', function () {
    config()->set('codicefiscale.update_token', 'expected-token');

    $this->post(route('internal.codice-fiscale-update'))
        ->assertForbidden();
});

it('runs the update command for an authorized scheduler request', function () {
    config()->set('codicefiscale.update_token', 'expected-token');
    Artisan::shouldReceive('call')
        ->once()
        ->with('codice-fiscale:update-places')
        ->andReturn(0);

    $this->post(route('internal.codice-fiscale-update'), [], [
        'X-Codicefiscale-Update-Token' => 'expected-token',
    ])->assertNoContent();
});

it('reports a conflict without a TypeError when a refresh is already running', function () {
    config()->set('codicefiscale.update_token', 'expected-token');

    $lock = Cache::lock('codice-fiscale:update-places', 900);
    $lock->get();

    try {
        $this->post(route('internal.codice-fiscale-update'), [], [
            'X-Codicefiscale-Update-Token' => 'expected-token',
        ])
            ->assertStatus(409)
            ->assertJson(['message' => 'A birthplace data refresh is already running.']);
    } finally {
        $lock->release();
    }
});

it('reports a failure without a TypeError when the update command exits non-zero', function () {
    config()->set('codicefiscale.update_token', 'expected-token');
    Artisan::shouldReceive('call')
        ->once()
        ->with('codice-fiscale:update-places')
        ->andReturn(1);

    $this->post(route('internal.codice-fiscale-update'), [], [
        'X-Codicefiscale-Update-Token' => 'expected-token',
    ])
        ->assertStatus(500)
        ->assertJson(['message' => 'Birthplace data refresh failed.']);
});

it('reports a failure without a TypeError when the update command throws', function () {
    config()->set('codicefiscale.update_token', 'expected-token');
    Artisan::shouldReceive('call')
        ->once()
        ->with('codice-fiscale:update-places')
        ->andThrow(new RuntimeException('boom'));

    $this->post(route('internal.codice-fiscale-update'), [], [
        'X-Codicefiscale-Update-Token' => 'expected-token',
    ])
        ->assertStatus(500)
        ->assertJson(['message' => 'Birthplace data refresh failed.']);
});

it('registers codice-fiscale:update-places even when the app is not running in console', function () {
    // laravel-codicefiscale only registers this command inside
    // `if ($this->app->runningInConsole())` (see CodiceFiscaleServiceProvider::boot()).
    // Artisan::call() from RefreshCodiceFiscaleDataController runs during a normal web
    // request, where runningInConsole() is false - so without AppServiceProvider
    // registering the command itself, Artisan::call() throws CommandNotFoundException.
    //
    // Every Pest test runs under the CLI SAPI, so runningInConsole() is always true in
    // this process and can't reproduce that. Boot a fresh, separate app process instead,
    // with APP_RUNNING_IN_CONSOLE forced false, to reproduce the real web-request context.
    $script = <<<'PHP'
        putenv('APP_RUNNING_IN_CONSOLE=false');

        require 'vendor/autoload.php';

        $app = require 'bootstrap/app.php';

        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        echo array_key_exists('codice-fiscale:update-places', $kernel->all()) ? 'registered' : 'missing';
        PHP;

    $result = Process::path(base_path())->run(['php', '-r', $script]);

    expect($result->successful())->toBeTrue($result->errorOutput());
    expect($result->output())->toBe('registered');
});
