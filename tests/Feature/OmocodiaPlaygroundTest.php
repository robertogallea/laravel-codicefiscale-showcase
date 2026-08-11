<?php

use App\Livewire\Explore\OmocodiaPlayground;
use Livewire\Livewire;
use Robertogallea\CodiceFiscale\CodiceFiscale;
use Robertogallea\CodiceFiscale\Omocodia\Omocodia;

it('renders the page at its own route', function () {
    $this->get(route('explore.omocodia'))->assertOk();
});

it('shows the correct canonical form and level for a non-omocodia code', function () {
    $code = validCodeForMilano();

    Livewire::test(OmocodiaPlayground::class)
        ->set('code', $code)
        ->call('loadCode')
        ->assertSet('canonicalCode', $code)
        ->assertSet('level', 0)
        ->assertSee($code);
});

it('shows the correct canonical form and a positive level for an already-substituted code', function () {
    $canonical = CodiceFiscale::from(validCodeForMilano());
    $omocodia = new Omocodia;

    $levelOneVariant = collect(iterator_to_array($omocodia->variants($canonical)))
        ->first(fn (CodiceFiscale $variant) => $omocodia->level($variant) === 1);

    Livewire::test(OmocodiaPlayground::class)
        ->set('code', $levelOneVariant->value())
        ->call('loadCode')
        ->assertSet('canonicalCode', $canonical->value())
        ->assertSet('level', 1);
});

it('updates the variant code and stays equivalent to the canonical form when a position is toggled', function () {
    $canonical = validCodeForMilano();

    $component = Livewire::test(OmocodiaPlayground::class)
        ->set('code', $canonical)
        ->call('loadCode')
        ->assertSet('variantCode', $canonical)
        ->assertSet('isEquivalent', true);

    $component->call('toggle', 0)
        ->assertSet('isEquivalent', true);

    $variantAfterToggle = $component->get('variantCode');

    expect($variantAfterToggle)->not->toBe($canonical)
        ->and(CodiceFiscale::from($variantAfterToggle)->isEquivalentTo(CodiceFiscale::from($canonical)))->toBeTrue();

    // Toggling the same position back off returns to the canonical form.
    $component->call('toggle', 0)
        ->assertSet('variantCode', $canonical)
        ->assertSet('isEquivalent', true);
});

it('stays equivalent to the canonical form across every combination of toggled positions', function () {
    $canonical = validCodeForMilano();

    $component = Livewire::test(OmocodiaPlayground::class)
        ->set('code', $canonical)
        ->call('loadCode');

    // Toggle every one of the 7 positions on, one at a time, checking
    // isEquivalentTo() stays true at each intermediate combination -
    // not just a single-position toggle, and not just the extremes.
    foreach (range(0, 6) as $index) {
        $component->call('toggle', $index)
            ->assertSet('isEquivalent', true);
    }

    // All 7 positions are now substituted (level 7, the other extreme).
    expect(
        (new Omocodia)->level(CodiceFiscale::from($component->get('variantCode')))
    )->toBe(7);

    // Toggle every position back off; still equivalent throughout, and
    // back to the canonical form (level 0) at the end.
    foreach (range(0, 6) as $index) {
        $component->call('toggle', $index)
            ->assertSet('isEquivalent', true);
    }

    $component->assertSet('variantCode', $canonical);
});

it('keeps the eligible-position and substitution-letter assumptions in sync with the real package', function () {
    // Guards against the app's own hardcoded position/letter constants
    // (needed because Omocodia has no public API to build one chosen
    // variant directly) silently drifting from the package's real
    // behavior - a mismatch here fails loudly instead of quietly
    // mislabeling a toggle. Uses the component directly (its own
    // public properties/methods, not the Livewire test harness) since
    // this is a pure-logic check across all 128 variants, not a
    // rendering/wiring one - the other tests above cover that surface.
    $canonical = CodiceFiscale::from(validCodeForMilano());
    $omocodia = new Omocodia;

    $signatures = [];
    $component = new OmocodiaPlayground;

    foreach ($omocodia->variants($canonical) as $variant) {
        $component->code = $variant->value();
        $component->loadCode();

        // The component's own loadCode() derives $toggles from the
        // variant using its position/letter assumptions; the popcount
        // of that signature must equal the package's own level().
        expect(array_sum($component->toggles))->toBe($omocodia->level($variant));

        $signatures[] = $component->toggles;
    }

    // All 128 variants must map to 128 distinct signatures - if the
    // assumed positions/letters were wrong, some variants would
    // collide onto the same signature (or none would, incoherently).
    // (implode() on a boolean array stringifies false as '', not '0' -
    // map explicitly first or every false position silently vanishes.)
    $signatureStrings = array_map(
        fn ($signature) => implode('', array_map(fn ($bit) => $bit ? '1' : '0', $signature)),
        $signatures,
    );
    expect(array_unique($signatureStrings))->toHaveCount(128);
});

it('shows a clear error for a structurally invalid code', function () {
    Livewire::test(OmocodiaPlayground::class)
        ->set('code', 'ABC')
        ->call('loadCode')
        ->assertSet('canonicalCode', null)
        ->assertSee(__('messages.omocodia.invalid_format'));
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(OmocodiaPlayground::class)
        ->assertSeeText('Canonical form')
        ->assertSeeText('Year (1st digit)')
        ->set('code', 'ABC')
        ->call('loadCode')
        ->assertSeeText(__('messages.omocodia.invalid_format', [], 'en'));

    app()->setLocale('it');

    Livewire::test(OmocodiaPlayground::class)
        ->assertSeeText('Forma canonica')
        ->assertSeeText('Anno (1° cifra)')
        ->set('code', 'ABC')
        ->call('loadCode')
        ->assertSeeText(__('messages.omocodia.invalid_format', [], 'it'));
});
