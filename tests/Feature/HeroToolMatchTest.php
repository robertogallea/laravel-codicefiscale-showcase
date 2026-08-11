<?php

use App\Livewire\HeroTool;
use Livewire\Livewire;

function decodedTestComponent()
{
    return Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', validCodeForMilano())
        ->call('decode');
}

it('matches every field when all provided data matches the decoded code\'s origin data', function () {
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchFirstName', 'Mario')
        ->set('matchLastName', 'Rossi')
        ->set('matchBirthDate', '1995-05-05')
        ->set('matchBirthPlaceCode', 'F205')
        ->set('matchGender', 'M')
        ->call('checkMatch')
        ->assertSet('matchStatuses', [
            'first_name' => 'matched',
            'last_name' => 'matched',
            'birth_date' => 'matched',
            'birth_place' => 'matched',
            'gender' => 'matched',
        ])
        ->assertSeeText(__('messages.decode.match.matched'));
});

it('shows only the deliberately wrong field as mismatched, the rest matched', function () {
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchFirstName', 'Mario')
        ->set('matchLastName', 'Rossi')
        ->set('matchBirthDate', '1995-05-05')
        ->set('matchBirthPlaceCode', 'F205')
        ->set('matchGender', 'F')
        ->call('checkMatch')
        ->assertSet('matchStatuses.gender', 'mismatched')
        ->assertSet('matchStatuses.first_name', 'matched')
        ->assertSet('matchStatuses.last_name', 'matched')
        ->assertSet('matchStatuses.birth_date', 'matched')
        ->assertSet('matchStatuses.birth_place', 'matched')
        ->assertSeeText(__('messages.decode.match.mismatched'));
});

it('shows a field left blank as skipped, distinct from matched or mismatched', function () {
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchFirstName', 'Mario')
        ->set('matchLastName', 'Rossi')
        ->set('matchBirthDate', '1995-05-05')
        ->set('matchBirthPlaceCode', null)
        ->set('matchGender', 'M')
        ->call('checkMatch')
        ->assertSet('matchStatuses.birth_place', 'skipped')
        ->assertSet('matchStatuses.first_name', 'matched')
        ->assertSet('matchStatuses.last_name', 'matched')
        ->assertSet('matchStatuses.birth_date', 'matched')
        ->assertSet('matchStatuses.gender', 'matched')
        ->assertSeeText(__('messages.decode.match.skipped'));
});

it('shows a blank birth date as skipped, not as today\'s date mismatching', function () {
    // Regression: new DateTimeImmutable('') resolves to "now" rather
    // than throwing, and Laravel's "nullable" rule doesn't coerce ''
    // to null - naively checking !== null on the validated value would
    // silently turn a blank date into "today", which almost always
    // mismatches instead of being skipped.
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchFirstName', 'Mario')
        ->set('matchLastName', 'Rossi')
        ->set('matchBirthDate', '')
        ->set('matchBirthPlaceCode', 'F205')
        ->set('matchGender', 'M')
        ->call('checkMatch')
        ->assertSet('matchStatuses.birth_date', 'skipped')
        ->assertSet('matchStatuses.first_name', 'matched')
        ->assertSet('matchStatuses.last_name', 'matched')
        ->assertSet('matchStatuses.birth_place', 'matched')
        ->assertSet('matchStatuses.gender', 'matched');
});

it('rejects a malformed match birthplace code instead of crashing', function () {
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchBirthPlaceCode', 'ND')
        ->call('checkMatch')
        ->assertSet('matchStatuses', null)
        ->assertHasErrors('matchBirthPlaceCode');
});

it('renders the person-matching disclosure correctly in both English and Italian', function () {
    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->assertSeeText(__('messages.decode.match.toggle', [], 'en'));

    app()->setLocale('it');

    decodedTestComponent()
        ->set('checkAgainstPerson', true)
        ->set('matchFirstName', 'Mario')
        ->call('checkMatch')
        ->assertSeeText(__('messages.decode.match.toggle', [], 'it'))
        ->assertSeeText(__('messages.decode.match.matched', [], 'it'))
        ->assertSeeText(__('messages.decode.match.skipped', [], 'it'));
});
