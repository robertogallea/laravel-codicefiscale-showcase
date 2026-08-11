<?php

use App\Livewire\HeroTool;
use Livewire\Livewire;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Checksum;
use Robertogallea\CodiceFiscale\Generation\Generator;

function validCodeForMilano(): string
{
    $person = new Person(
        firstName: 'Mario',
        lastName: 'Rossi',
        birthDate: new DateTimeImmutable('1995-05-05'),
        birthPlace: BirthPlaceCode::from('F205'),
        gender: Gender::Male,
    );

    return (new Generator)->generate($person)->value();
}

function codeFromFirst15(string $first15): string
{
    return $first15.(new Checksum)->calculate($first15);
}

it('decodes a valid code, matching what Parser::parse() produces directly', function () {
    $code = validCodeForMilano();

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $code)
        ->call('decode')
        ->assertSet('decodeValid', true)
        ->assertSet('decodedCode', $code)
        ->assertSee(substr($code, 0, 3))
        ->assertSeeText('Male')
        ->assertSeeText('Milano')
        ->assertSeeText('1995');
});

it('shows a specific message for an invalid format', function () {
    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', 'ABC')
        ->call('decode')
        ->assertSet('decodeValid', false)
        ->assertSeeText(__('messages.decode.errors.invalid_format'));
});

it('shows a specific message for an invalid checksum', function () {
    $code = validCodeForMilano();
    $lastChar = substr($code, -1);
    $wrongChecksum = $lastChar === 'A' ? 'B' : 'A';
    $withWrongChecksum = substr($code, 0, 15).$wrongChecksum;

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $withWrongChecksum)
        ->call('decode')
        ->assertSet('decodeValid', false)
        ->assertSeeText(__('messages.decode.errors.invalid_checksum'));
});

it('shows a specific message for a nonexistent calendar date', function () {
    // Year 95, month letter B (February), day 31 - February never has 31 days.
    $code = codeFromFirst15('RSSMRA95B31F205');

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $code)
        ->call('decode')
        ->assertSet('decodeValid', false)
        ->assertSeeText(__('messages.decode.errors.invalid_date'));
});

it('shows a specific message for an unrecognized birthplace', function () {
    // A999 never appears in the fixture repository at all.
    $code = codeFromFirst15('RSSMRA95E05A999');

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $code)
        ->call('decode')
        ->assertSet('decodeValid', false)
        ->assertSeeText(__('messages.decode.errors.unknown_birth_place'));
});

it('shows a specific message for a birthplace not yet valid on the encoded date', function () {
    // Z999 only exists from 1990 onward; year 50 resolves to 1950.
    $code = codeFromFirst15('RSSMRA50E05Z999');

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $code)
        ->call('decode')
        ->assertSet('decodeValid', false)
        ->assertSeeText(__('messages.decode.errors.birth_place_not_valid_on_date'));
});

it('switches to Decode with the generated code pre-filled via "Verify this code"', function () {
    Livewire::test(HeroTool::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceCode', 'F205')
        ->call('generate')
        ->call('verifyGeneratedCode')
        ->assertSet('mode', 'decode')
        ->assertSet('decodeValid', true)
        ->assertSeeText('Milano');
});

it('opens directly into Decode, already showing the result, when loaded with ?cf= in the URL', function () {
    $code = validCodeForMilano();

    // assertSeeText alone would pass even if the Decode panel were the
    // hidden one client-side (Flux tabs render every panel's markup,
    // toggling visibility with CSS). The wire:snapshot embedded in the
    // response is the actual server-side component state for this
    // request, so asserting against it proves mode really became
    // 'decode' - not just that decoded content exists somewhere in the
    // page - without needing a real browser.
    $this->get('/?cf='.$code)
        ->assertOk()
        ->assertSee('&quot;mode&quot;:&quot;decode&quot;', false)
        ->assertSee('&quot;decodeValid&quot;:true', false)
        ->assertSeeText('Milano')
        ->assertSeeText('1995');
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(HeroTool::class)
        ->assertSeeText('Decode');

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', 'ABC')
        ->call('decode')
        ->assertSeeText(__('messages.decode.errors.invalid_format', [], 'en'));

    app()->setLocale('it');

    Livewire::test(HeroTool::class)
        ->assertSeeText('Decodifica');

    $code = validCodeForMilano();

    Livewire::test(HeroTool::class)
        ->set('mode', 'decode')
        ->set('decodeInput', $code)
        ->call('decode')
        ->assertSeeText(__('messages.decode.result.gender_male', [], 'it'));
});
