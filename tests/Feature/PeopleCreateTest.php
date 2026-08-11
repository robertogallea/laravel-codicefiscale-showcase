<?php

use App\Livewire\People\PeopleCreate;
use App\Models\Person;
use Livewire\Livewire;

it('renders the page at its own route', function () {
    $this->get(route('people.create'))->assertOk();
});

it('creates a person and redirects to the show page when the code matches the entered data', function () {
    $code = validCodeForMilano();

    $component = Livewire::test(PeopleCreate::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceCode', 'F205')
        ->set('codiceFiscale', $code)
        ->call('create');

    $person = Person::where('codice_fiscale', $code)->first();

    expect($person)->not->toBeNull()
        ->and($person->first_name)->toBe('Mario')
        ->and($person->last_name)->toBe('Rossi');

    $component->assertRedirect(route('people.show', $person));
});

it('rejects a code that mismatches the entered gender with a field-specific message, not a generic error', function () {
    // validCodeForMilano() encodes Male - submitting Female here is a
    // genuine cross-field mismatch, not a malformed code.
    $code = validCodeForMilano();

    Livewire::test(PeopleCreate::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'F')
        ->set('birthPlaceCode', 'F205')
        ->set('codiceFiscale', $code)
        ->call('create')
        ->assertHasErrors('codiceFiscale')
        ->assertSeeText('gender');

    expect(Person::where('codice_fiscale', $code)->exists())->toBeFalse();
});

it('rejects a structurally invalid code with the rule\'s standard message, not a mismatch message', function () {
    Livewire::test(PeopleCreate::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceCode', 'F205')
        ->set('codiceFiscale', 'not-a-code')
        ->call('create')
        ->assertHasErrors('codiceFiscale');
});

it('makes the fluent cross-field rule explicit in the page copy', function () {
    $this->get(route('people.create'))->assertSeeText(__('messages.people.create.tagline'));
});

it('renders the page copy correctly in both English and Italian', function () {
    // Heading/tagline live in the wrapping page, checked via a real
    // HTTP request - matches HomePageTest/PeopleIndexTest's
    // locale-switching convention.
    $this->get(route('people.create'))->assertSeeText(__('messages.people.create.heading', [], 'en'));

    $this->withSession(['locale' => 'it'])
        ->get(route('people.create'))
        ->assertSeeText(__('messages.people.create.heading', [], 'it'));
});

it('renders the form copy correctly in both English and Italian', function () {
    // Kept in its own test using only app()->setLocale(), never mixed
    // with withSession()'s HTTP-level locale switching above: the
    // SetLocale middleware's App::setLocale() call is process-wide and
    // outlives the request that triggered it, so a Livewire::test()
    // call made afterwards in the same test would silently render
    // under whichever locale that HTTP request last left the app in.
    Livewire::test(PeopleCreate::class)
        ->assertSeeText(__('messages.people.fields.first_name', [], 'en'));

    app()->setLocale('it');

    Livewire::test(PeopleCreate::class)
        ->assertSeeText(__('messages.people.fields.first_name', [], 'it'));
});
