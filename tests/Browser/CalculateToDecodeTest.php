<?php

it('generates a code in Calculate, verifies it into Decode, and the decoded result matches - end to end in a real browser', function () {
    $code = validCodeForMilano();

    $page = visit('/');

    $page->type('firstName', 'Mario')
        ->type('lastName', 'Rossi')
        ->type('birthDate', '1995-05-05')
        ->type('birthPlaceQuery', 'Milano');

    $page->click('[wire\:key="birth-place-F205"]');

    $page->click('Generate');

    $page->assertSee($code);

    // Regression guard: @js() doesn't compile inside a Blade component-tag
    // attribute, so this button's Alpine handler threw a JS syntax error on
    // click - invisible to Livewire::test(), which never runs real JS.
    $page->click('Copy')
        ->assertNoJavaScriptErrors();

    $page->click('Verify this code →');

    $page->assertQueryStringHas('cf', $code)
        ->assertSee($code)
        ->assertSeeText('Male')
        ->assertSeeText('1995');

    // Fresh GET against the shared ?cf= URL: HeroTool's mode/decodeInput are
    // Url-bound Livewire properties with client-side pushState, not a server
    // redirect or session state, so this confirms that hand-off rehydrates
    // Decode correctly and the JS runtime stays error-free on reload.
    $page->reload()
        ->assertSee($code)
        ->assertSeeText('Male')
        ->assertSeeText('1995')
        ->assertNoJavaScriptErrors();
});
