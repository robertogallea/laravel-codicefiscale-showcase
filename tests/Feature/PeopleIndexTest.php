<?php

use App\Livewire\People\PeopleIndex;
use App\Models\Person;
use Livewire\Livewire;

it('renders the page at its own route', function () {
    $this->get(route('people.index'))->assertOk();
});

it('lists seeded people', function () {
    Person::factory()->create(['first_name' => 'Mario', 'last_name' => 'Rossi']);

    Livewire::test(PeopleIndex::class)
        ->assertSeeText('Mario Rossi');
});

it('paginates rather than listing every row on one page', function () {
    Person::factory()->count(15)->create();

    Livewire::test(PeopleIndex::class)
        ->assertViewHas('people', function ($people) {
            return $people->count() < 15 && $people->total() === 15;
        });
});

it('moves to the next page of results', function () {
    Person::factory()->count(15)->sequence(
        ...array_map(fn (int $i) => ['last_name' => sprintf('Surname%02d', $i)], range(1, 15))
    )->create();

    $firstPageFirstPerson = Person::query()->orderBy('last_name')->first();
    $secondPagePerson = Person::query()->orderBy('last_name')->skip(10)->first();

    Livewire::test(PeopleIndex::class)
        ->assertSeeText($firstPageFirstPerson->last_name)
        ->assertDontSeeText($secondPagePerson->last_name)
        ->call('nextPage')
        ->assertSeeText($secondPagePerson->last_name)
        ->assertDontSeeText($firstPageFirstPerson->last_name);
});

it('shows an empty-state message when no people are seeded', function () {
    Livewire::test(PeopleIndex::class)
        ->assertSeeText(__('messages.people.empty'));
});

it('renders the page copy correctly in both English and Italian', function () {
    // Heading/tagline live in the wrapping page (resources/views/people/index.blade.php),
    // not the Livewire component's own view - checked via a real HTTP
    // request, matching HomePageTest's locale-switching convention.
    $this->get(route('people.index'))->assertSeeText(__('messages.people.heading', [], 'en'));

    $this->withSession(['locale' => 'it'])
        ->get(route('people.index'))
        ->assertSeeText(__('messages.people.heading', [], 'it'));
});

it('renders the listing copy correctly in both English and Italian', function () {
    Livewire::test(PeopleIndex::class)
        ->assertSeeText(__('messages.people.empty', [], 'en'));

    app()->setLocale('it');

    Livewire::test(PeopleIndex::class)
        ->assertSeeText(__('messages.people.empty', [], 'it'));
});
