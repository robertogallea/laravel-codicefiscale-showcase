<?php

use App\Livewire\HeroTool;
use Livewire\Livewire;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Generator;

it('generates a code matching what Generator::generate() produces directly', function () {
    $person = new Person(
        firstName: 'Mario',
        lastName: 'Rossi',
        birthDate: new DateTimeImmutable('1995-05-05'),
        birthPlace: BirthPlaceCode::from('F205'),
        gender: Gender::Male,
    );
    $expected = (new Generator)->generate($person)->value();

    Livewire::test(HeroTool::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceQuery', 'Milano')
        ->set('birthPlaceCode', 'F205')
        ->call('generate')
        ->assertSet('generatedCode', $expected)
        ->assertSee(substr($expected, 0, 3))
        ->assertSee(substr($expected, 15, 1));
});

it('surfaces domestic and foreign birthplace matches from the same search field, cadastral code included', function () {
    $component = Livewire::test(HeroTool::class)
        ->set('birthPlaceQuery', 'mila');

    expect($component->get('birthPlaceOptions'))
        ->toHaveCount(1)
        ->and($component->get('birthPlaceOptions')[0]['label'])->toBe('Milano (F205)');

    $component->set('birthPlaceQuery', 'stati uniti');

    expect($component->get('birthPlaceOptions'))
        ->toHaveCount(1)
        ->and($component->get('birthPlaceOptions')[0]['label'])->toBe('Stati Uniti d\'America (Z404)');
});

it('surfaces the exact match for a full cadastral code query', function () {
    // "F205" doesn't textually appear in "Milano" - this only resolves
    // through the exact BirthPlaceRepository::find() lookup path.
    $options = Livewire::test(HeroTool::class)
        ->set('birthPlaceQuery', 'F205')
        ->get('birthPlaceOptions');

    expect($options)->toHaveCount(1)
        ->and($options[0]['label'])->toBe('Milano (F205)');
});

it('deduplicates era-records that share a birthplace code into one option', function () {
    // The fixture repository holds two era-records for F205 (Milano) -
    // an old province and the current one - to mirror the real trap
    // where a renamed/reprovinced comune shares one code across eras.
    $options = Livewire::test(HeroTool::class)
        ->set('birthPlaceQuery', 'milano')
        ->get('birthPlaceOptions');

    expect($options)->toHaveCount(1)
        ->and($options[0]['code'])->toBe('F205')
        ->and($options[0]['label'])->toBe('Milano (F205)');
});

it('does not search when the query is empty', function () {
    Livewire::test(HeroTool::class)
        ->set('birthPlaceQuery', '')
        ->assertSet('birthPlaceOptions', []);
});

it('shows a clear error instead of generating when no birthplace was resolved', function () {
    Livewire::test(HeroTool::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceCode', null)
        ->call('generate')
        ->assertSet('generatedCode', null)
        ->assertHasErrors('birthPlaceCode');
});

it('rejects a malformed birthplace code instead of crashing', function () {
    // Reproduces a real failure seen against the combobox: a value that
    // never matches BirthPlaceCode's own [A-Z][0-9]{3} shape reaching
    // birthPlaceCode (e.g. leftover free-typed text) must be caught by
    // validation, not thrown as an uncaught InvalidBirthPlaceCodeException.
    Livewire::test(HeroTool::class)
        ->set('firstName', 'Mario')
        ->set('lastName', 'Rossi')
        ->set('birthDate', '1995-05-05')
        ->set('gender', 'M')
        ->set('birthPlaceCode', 'ND')
        ->call('generate')
        ->assertSet('generatedCode', null)
        ->assertHasErrors('birthPlaceCode');
});

it('requires the other personal fields before generating', function () {
    Livewire::test(HeroTool::class)
        ->set('birthPlaceCode', 'F205')
        ->call('generate')
        ->assertSet('generatedCode', null)
        ->assertHasErrors(['firstName', 'lastName', 'birthDate']);
});

it('renders correctly in both English and Italian', function () {
    Livewire::test(HeroTool::class)->assertSeeText('Generate');

    app()->setLocale('it');

    Livewire::test(HeroTool::class)->assertSeeText('Genera');
});
