<?php

it('shows a back-to-hero link in the header on every feature page', function () {
    $pages = [
        route('explore.omocodia'),
        route('explore.repositories'),
        route('explore.birth-date-resolver'),
        route('explore.birthplace-history'),
        route('explore.validation-rule'),
        route('people.index'),
    ];

    foreach ($pages as $page) {
        $this->get($page)
            ->assertOk()
            ->assertSee('href="'.route('home').'"', false);
    }
});

it('shows the quadro eyebrow and prev/next rail linking to the right neighbours', function () {
    // Catalog order: calculate, decode, person_matching, omocodia,
    // repository_swap, birth_date_resolver, birthplace_history,
    // validation_rule, people - omocodia's neighbours are
    // person-matching (prev) and repository-swap (next).
    $response = $this->get(route('explore.omocodia'))->assertOk();

    $response->assertSeeText('OM')
        ->assertSeeText(__('messages.nav.position', ['position' => 4, 'total' => 9]))
        ->assertSee('href="'.e(route('home', ['mode' => 'decode', 'match' => 1])).'"', false)
        ->assertSeeText(__('messages.home.features.person_matching.heading'))
        ->assertSee('href="'.route('explore.repositories').'"', false)
        ->assertSeeText(__('messages.home.features.repository_swap.heading'));
});

it('wraps around at the ends of the catalog', function () {
    // People is last in the catalog - next wraps back to Calculate,
    // prev is Validation-rule.
    $response = $this->get(route('people.index'))->assertOk();

    $response->assertSeeText('PD')
        ->assertSeeText(__('messages.nav.position', ['position' => 9, 'total' => 9]))
        ->assertSee('href="'.route('explore.validation-rule').'"', false)
        ->assertSee('href="'.route('home').'"', false);
});

it('does not show the feature-page prev/next rail on the hero/home page itself', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSeeText(__('messages.nav.back_to_hero'));
});
