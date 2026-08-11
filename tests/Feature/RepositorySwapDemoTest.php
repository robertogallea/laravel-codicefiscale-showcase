<?php

use App\Livewire\Explore\RepositorySwapDemo;
use App\Support\BirthPlaces\DemoBirthPlaceRepository;
use Livewire\Livewire;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;

it('never resolves the Demo repository as the container\'s default BirthPlaceRepository binding', function () {
    // Guards the guarantee this whole demo depends on: the Real
    // repository always comes from the container (app()), the Demo
    // one is always a plain `new` - if a future change ever bound
    // DemoBirthPlaceRepository into the container, it would silently
    // leak into the hero tool, which must always use real data.
    expect(app(BirthPlaceRepository::class))->not->toBeInstanceOf(DemoBirthPlaceRepository::class);
});

it('renders the page at its own route', function () {
    $this->get(route('explore.repositories'))->assertOk();
});

it('resolves the demonstration code to the fictional place under the demo repository', function () {
    Livewire::test(RepositorySwapDemo::class)
        ->set('repository', 'demo')
        ->call('resolve')
        ->assertSet('valid', true)
        ->assertSeeText('Atlantis');
});

it('fails to resolve the same demonstration code under the real repository', function () {
    // The demonstration code's birthplace only exists in the demo
    // repository (an "X"-prefixed code, never real ANPR/MAECI data) -
    // under the real repository it's an unrecognized birthplace, the
    // same fixed code producing genuinely different, not just
    // differently-worded, outcomes.
    Livewire::test(RepositorySwapDemo::class)
        ->set('repository', 'real')
        ->call('resolve')
        ->assertSet('valid', false)
        ->assertSeeText(__('messages.decode.errors.unknown_birth_place'));
});

it('keeps the same demonstration code across both repositories', function () {
    $component = Livewire::test(RepositorySwapDemo::class);
    $code = $component->get('code');

    $component->set('repository', 'real')->call('resolve');
    expect($component->get('code'))->toBe($code);

    $component->set('repository', 'demo')->call('resolve');
    expect($component->get('code'))->toBe($code);
});

it('makes the fictional repository explicit in the page copy', function () {
    Livewire::test(RepositorySwapDemo::class)
        ->assertSeeText(__('messages.repository_swap.demo_notice'));
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(RepositorySwapDemo::class)
        ->set('repository', 'demo')
        ->call('resolve')
        ->assertSeeText(__('messages.repository_swap.real_label', [], 'en'));

    app()->setLocale('it');

    Livewire::test(RepositorySwapDemo::class)
        ->set('repository', 'real')
        ->call('resolve')
        ->assertSeeText(__('messages.decode.errors.unknown_birth_place', [], 'it'));
});
