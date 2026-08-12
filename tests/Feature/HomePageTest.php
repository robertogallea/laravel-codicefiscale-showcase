<?php

it('renders successfully in the default (English) locale', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Codice Fiscale Studio')
        ->assertSee('A showcase of every laravel-codicefiscale v3 feature.');
});

it('renders successfully in Italian once the locale is switched', function () {
    $this->withSession(['locale' => 'it'])
        ->get('/')
        ->assertOk()
        ->assertSee('Una vetrina di tutte le funzionalità di laravel-codicefiscale v3.');
});

it('generates HTTPS asset URLs behind a trusted TLS-terminating proxy', function () {
    $this->withServerVariables([
        'REMOTE_ADDR' => '10.0.0.1',
        'HTTP_X_FORWARDED_FOR' => '203.0.113.1',
        'HTTP_X_FORWARDED_HOST' => 'showcase.example.test',
        'HTTP_X_FORWARDED_PROTO' => 'https',
    ])->get('/')
        ->assertOk()
        ->assertSee('https://showcase.example.test/build/assets/', false)
        ->assertDontSee('http://showcase.example.test/build/assets/', false);
});

it('shows the birthplace data freshness date read from the package database file', function () {
    $path = tempnam(sys_get_temp_dir(), 'places').'.sqlite';
    touch($path, mktime(0, 0, 0, 1, 15, 2026));
    config(['codicefiscale.database.path' => $path]);

    $this->get('/')->assertSee('15 Jan 2026', false);

    unlink($path);
});
