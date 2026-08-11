<?php

it('has exactly one card per feature area from the parent spec, every one linking to a working page', function () {
    $response = $this->get(route('home'))->assertOk();

    $expectedHeadings = [
        __('messages.home.features.calculate.heading'),
        __('messages.home.features.decode.heading'),
        __('messages.home.features.person_matching.heading'),
        __('messages.home.features.omocodia.heading'),
        __('messages.home.features.repository_swap.heading'),
        __('messages.home.features.birth_date_resolver.heading'),
        __('messages.home.features.birthplace_history.heading'),
        __('messages.home.features.validation_rule.heading'),
        __('messages.home.features.people.heading'),
    ];

    expect($expectedHeadings)->toHaveCount(9)
        ->and(array_unique($expectedHeadings))->toHaveCount(9);

    foreach ($expectedHeadings as $heading) {
        $response->assertSeeText($heading);
    }

    $routes = [
        route('home'),
        route('home', ['mode' => 'decode']),
        route('home', ['mode' => 'decode', 'match' => 1]),
        route('explore.omocodia'),
        route('explore.repositories'),
        route('explore.birth-date-resolver'),
        route('explore.birthplace-history'),
        route('explore.validation-rule'),
        route('people.index'),
    ];

    foreach ($routes as $route) {
        $this->get($route)->assertOk();
    }
});

it('deep-links the Decode card straight into the Decode mode', function () {
    // Both tab panels' markup is always server-rendered (Flux tabs
    // toggle visibility with CSS, not conditional rendering), so
    // assertSeeText alone can't prove which tab is actually active -
    // asserting against the embedded wire:snapshot's own mode value
    // does, per the same pattern HeroToolDecodeTest already uses for
    // the ?cf= deep link.
    $this->get(route('home', ['mode' => 'decode']))
        ->assertOk()
        ->assertSee('&quot;mode&quot;:&quot;decode&quot;', false);
});

it('deep-links the person-matching card into Decode with the matching disclosure open', function () {
    $this->get(route('home', ['mode' => 'decode', 'match' => 1]))
        ->assertOk()
        ->assertSee('&quot;mode&quot;:&quot;decode&quot;', false)
        ->assertSee('&quot;checkAgainstPerson&quot;:true', false);
});

it('leaves the Calculate card on the default mode with the matching disclosure closed', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('&quot;mode&quot;:&quot;calculate&quot;', false)
        ->assertSee('&quot;checkAgainstPerson&quot;:false', false);
});

it('renders every card\'s own heading and description correctly in both English and Italian', function () {
    $featureKeys = [
        'calculate', 'decode', 'person_matching', 'omocodia', 'repository_swap',
        'birth_date_resolver', 'birthplace_history', 'validation_rule', 'people',
    ];

    $response = $this->get(route('home'))->assertSeeText(__('messages.home.features_heading', [], 'en'));

    foreach ($featureKeys as $key) {
        $response
            ->assertSeeText(__("messages.home.features.$key.heading", [], 'en'))
            ->assertSeeText(__("messages.home.features.$key.description", [], 'en'));
    }

    $response = $this->withSession(['locale' => 'it'])
        ->get(route('home'))
        ->assertSeeText(__('messages.home.features_heading', [], 'it'));

    foreach ($featureKeys as $key) {
        $response
            ->assertSeeText(__("messages.home.features.$key.heading", [], 'it'))
            ->assertSeeText(__("messages.home.features.$key.description", [], 'it'));
    }
});
