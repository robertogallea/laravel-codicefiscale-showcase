<?php

use App\Livewire\Explore\BirthDateResolverPlayground;
use Livewire\Livewire;

it('renders the page at its own route', function () {
    $this->get(route('explore.birth-date-resolver'))->assertOk();
});

it('shows both candidate years for a genuinely ambiguous input', function () {
    Livewire::test(BirthDateResolverPlayground::class)
        ->set('month', 5)
        ->set('day', 20)
        ->set('twoDigitYear', '26')
        ->call('resolve')
        ->assertSet('possibleYears', [1926, 2026])
        ->assertSee('1926')
        ->assertSee('2026');
});

it('resolves a genuinely ambiguous input to the younger candidate when both are plausible', function () {
    // Milano (F205) is valid across both 1926 and 2026, so nothing
    // distinguishes the two on birthplace history - the resolver falls
    // through to preferring the younger candidate.
    Livewire::test(BirthDateResolverPlayground::class)
        ->set('month', 5)
        ->set('day', 20)
        ->set('twoDigitYear', '26')
        ->set('maxAge', 120)
        ->set('birthPlaceCode', 'F205')
        ->call('resolve')
        ->assertSet('resolvedDate', '2026-05-20');
});

it('resolves to no plausible date once maxAge excludes the only ever-plausible candidate', function () {
    // Year "50": 2050 is always in the future relative to today, so it
    // never becomes a plausible candidate regardless of maxAge - only
    // 1950 can ever be resolved, and only while maxAge covers its age.
    $component = Livewire::test(BirthDateResolverPlayground::class)
        ->set('month', 5)
        ->set('day', 20)
        ->set('twoDigitYear', '50')
        ->set('maxAge', 120)
        ->set('birthPlaceCode', 'F205')
        ->call('resolve')
        ->assertSet('resolvedDate', '1950-05-20');

    $ageOf1950Candidate = (new DateTimeImmutable('today'))->diff(new DateTimeImmutable('1950-05-20'))->y;

    $component->set('maxAge', $ageOf1950Candidate - 1)
        ->call('resolve')
        ->assertSet('resolvedDate', null)
        ->assertSeeText(__('messages.birth_date_resolver.no_plausible_date'));
});

it('flips which of the two live candidates is resolved as maxAge changes', function () {
    // The genuine article: both 1926 and 2026 are plausible at a large
    // maxAge, and Y001 (valid only 1861-1950, per the fixture repo) is
    // valid at exactly the older one - the birthplace-history tiebreak
    // resolves the ambiguity in favor of 1926 over the usual younger-
    // wins default. Lowering maxAge below 1926's age excludes it,
    // leaving only 2026 - the SAME resolver call now answers
    // differently for the SAME two live candidates, not just "some
    // date" vs. "no date".
    $component = Livewire::test(BirthDateResolverPlayground::class)
        ->set('month', 5)
        ->set('day', 20)
        ->set('twoDigitYear', '26')
        ->set('maxAge', 120)
        ->set('birthPlaceCode', 'Y001')
        ->call('resolve')
        ->assertSet('possibleYears', [1926, 2026])
        ->assertSet('resolvedDate', '1926-05-20');

    $ageOf1926Candidate = (new DateTimeImmutable('today'))->diff(new DateTimeImmutable('1926-05-20'))->y;

    $component->set('maxAge', $ageOf1926Candidate - 1)
        ->call('resolve')
        ->assertSet('resolvedDate', '2026-05-20');
});

it('shows a single, non-ambiguous candidate when only one interpretation is a real calendar date', function () {
    // Year "00": 1900 was not a leap year, 2000 was - Feb 29 only
    // exists in one of the two centuries, so there's exactly one
    // calendar-valid candidate even though possibleYears() always
    // reports both raw years.
    Livewire::test(BirthDateResolverPlayground::class)
        ->set('month', 2)
        ->set('day', 29)
        ->set('twoDigitYear', '00')
        ->set('maxAge', 120)
        ->set('birthPlaceCode', 'F205')
        ->call('resolve')
        ->assertSet('possibleYears', [1900, 2000])
        ->assertSet('resolvedDate', '2000-02-29');
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(BirthDateResolverPlayground::class)
        ->assertSeeText(__('messages.birth_date_resolver.max_age_label', [], 'en'));

    app()->setLocale('it');

    Livewire::test(BirthDateResolverPlayground::class)
        ->assertSeeText(__('messages.birth_date_resolver.max_age_label', [], 'it'));
});
