<?php

it('switches the locale and redirects back', function () {
    $this->from('/')
        ->get('/locale/it')
        ->assertRedirect('/');

    expect(session('locale'))->toBe('it');
});

it('persists the switched locale across subsequent requests', function () {
    $this->get('/locale/it');

    $this->get('/')->assertSee('Una vetrina di tutte le funzionalità di laravel-codicefiscale v3.');
});

it('rejects an unsupported locale', function () {
    $this->get('/locale/fr')->assertNotFound();
});
