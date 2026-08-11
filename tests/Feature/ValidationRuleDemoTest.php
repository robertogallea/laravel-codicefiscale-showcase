<?php

it('renders the page at its own route', function () {
    $this->get(route('explore.validation-rule'))->assertOk();
});

it('passes a valid codice fiscale through a real $request->validate() call', function () {
    $code = validCodeForMilano();

    $response = $this->post(route('explore.validation-rule.store'), ['codice_fiscale' => $code]);

    $response
        ->assertRedirect(route('explore.validation-rule'))
        ->assertSessionHasNoErrors();

    $this->get(route('explore.validation-rule'))
        ->assertSeeText(__('messages.validation_rule.valid_message', ['code' => $code]));
});

it('fails an invalid codice fiscale with the rule\'s standard translated message', function () {
    // Asserted directly against the session right after the POST,
    // rather than re-rendering the followed redirect: the "array"
    // session driver phpunit.xml sets for tests re-boots the Store a
    // second time on any subsequent request within the same test
    // process, and Laravel's json-serialization error-bag marshaling
    // (Illuminate\Session\Store::marshalErrorBag()) only knows how to
    // rehydrate the array shape it itself wrote to storage - handed an
    // already-live ViewErrorBag object (as happens on this second,
    // same-process start()), it silently replaces it with an empty
    // one. Confirmed via a real browser/database-driver round-trip
    // (per .ai/rules/controllers.md) that the message renders
    // correctly outside this test-only artifact.
    $message = trans('codicefiscale::validation.invalid_format', ['attribute' => 'codice_fiscale']);

    $this
        ->from(route('explore.validation-rule'))
        ->post(route('explore.validation-rule.store'), ['codice_fiscale' => 'not-a-code'])
        ->assertRedirect(route('explore.validation-rule'))
        ->assertSessionHasErrors(['codice_fiscale' => $message]);
});

it('makes the rule-vs-domain-Validator distinction explicit in the page copy', function () {
    $this->get(route('explore.validation-rule'))
        ->assertSeeText(__('messages.validation_rule.tagline'));
});

it('renders correctly in both English and Italian', function () {
    $this->get(route('explore.validation-rule'))
        ->assertSeeText(__('messages.validation_rule.heading', [], 'en'))
        ->assertSeeText(__('messages.validation_rule.tagline', [], 'en'));

    $this->withSession(['locale' => 'it'])
        ->get(route('explore.validation-rule'))
        ->assertSeeText(__('messages.validation_rule.heading', [], 'it'))
        ->assertSeeText(__('messages.validation_rule.tagline', [], 'it'));
});
