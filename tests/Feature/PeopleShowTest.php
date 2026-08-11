<?php

use App\Models\Person;

it('renders the stored person\'s fields', function () {
    $person = Person::factory()->create(['first_name' => 'Mario', 'last_name' => 'Rossi']);

    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertSeeText('Mario Rossi');
});

it('re-parses the stored codice_fiscale via the same breakdown component used by the hero tool, matching the stored data', function () {
    $code = validCodeForMilano();
    $person = Person::factory()->create(['codice_fiscale' => $code]);

    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertSee(substr($code, 0, 3)) // the breakdown component's surname-code segment
        ->assertSeeText('1995-05-05')
        ->assertSeeText(__('messages.decode.result.gender_male'));
});

it('renders correctly in both English and Italian', function () {
    $person = Person::factory()->create(['codice_fiscale' => validCodeForMilano()]);

    $this->get(route('people.show', $person))
        ->assertSeeText(__('messages.people.show.reparse_heading', [], 'en'));

    $this->withSession(['locale' => 'it'])
        ->get(route('people.show', $person))
        ->assertSeeText(__('messages.people.show.reparse_heading', [], 'it'));
});
