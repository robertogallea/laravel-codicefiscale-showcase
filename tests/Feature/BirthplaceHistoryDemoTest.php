<?php

use App\Livewire\Explore\BirthplaceHistoryDemo;
use Livewire\Livewire;

it('renders the page at its own route', function () {
    $this->get(route('explore.birthplace-history'))->assertOk();
});

it('resolves the pre-rename identity for a date before the change', function () {
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '1900-01-01')
        ->call('resolve')
        ->assertSet('historyResult.status', 'valid')
        ->assertSet('historyResult.name', 'Piperno')
        ->assertSet('historyResult.province', 'RM')
        ->assertSeeText('Piperno');
});

it('resolves the renamed, not-yet-reprovinced identity for a date between the two real changes', function () {
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '1930-01-01')
        ->call('resolve')
        ->assertSet('historyResult.status', 'valid')
        ->assertSet('historyResult.name', 'Priverno')
        ->assertSet('historyResult.province', 'RM')
        ->assertSeeText('Priverno');
});

it('resolves the current, reprovinced identity for a recent date', function () {
    // G698's real second change: reprovinced RM to LT on 1934-10-26.
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '2000-01-01')
        ->call('resolve')
        ->assertSet('historyResult.status', 'valid')
        ->assertSet('historyResult.name', 'Priverno')
        ->assertSet('historyResult.province', 'LT')
        ->assertSeeText('LT');
});

it('resolves a date exactly on the rename boundary to the new identity', function () {
    // wasValidOn() is [validFrom, validTo) - the rename date itself
    // belongs to the new era, not the old one.
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '1928-09-09')
        ->call('resolve')
        ->assertSet('historyResult.name', 'Priverno')
        ->assertSet('historyResult.province', 'RM');
});

it('distinguishes a code that never existed from one that existed but not on this date', function () {
    Livewire::test(BirthplaceHistoryDemo::class)
        // Before the comune's own earliest era-record: existed, wrong date.
        ->set('date', '1850-01-01')
        ->call('resolve')
        ->assertSet('historyResult.status', 'not_valid_on_date')
        ->assertSet('neverExistedResult.status', 'never_existed')
        ->assertSeeText(__('messages.birthplace_history.not_valid_on_date'))
        ->assertSeeText(__('messages.birthplace_history.never_existed'));
});

it('shows a neutral no-date state without crashing when the date field is blanked', function () {
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '')
        ->call('resolve')
        ->assertSet('historyResult.status', 'no_date')
        ->assertSet('neverExistedResult.status', 'no_date')
        ->assertSeeText(__('messages.birthplace_history.no_date'));
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '1850-01-01')
        ->call('resolve')
        ->assertSeeText(__('messages.birthplace_history.date_label', [], 'en'))
        ->assertSeeText(__('messages.birthplace_history.not_valid_on_date', [], 'en'))
        ->assertSeeText(__('messages.birthplace_history.never_existed', [], 'en'));

    app()->setLocale('it');

    Livewire::test(BirthplaceHistoryDemo::class)
        ->set('date', '1850-01-01')
        ->call('resolve')
        ->assertSeeText(__('messages.birthplace_history.date_label', [], 'it'))
        ->assertSeeText(__('messages.birthplace_history.not_valid_on_date', [], 'it'))
        ->assertSeeText(__('messages.birthplace_history.never_existed', [], 'it'));
});
