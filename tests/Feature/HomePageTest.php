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

it('shows the birthplace data freshness date read from the package database file', function () {
    $path = tempnam(sys_get_temp_dir(), 'places').'.sqlite';
    touch($path, mktime(0, 0, 0, 1, 15, 2026));
    config(['codicefiscale.database.path' => $path]);

    $this->get('/')->assertSee('15 Jan 2026', false);

    unlink($path);
});
