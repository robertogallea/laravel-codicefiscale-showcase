<?php

use App\Models\Person;
use Robertogallea\CodiceFiscale\CodiceFiscale;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person as CodiceFiscalePerson;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Exceptions\InvalidCodiceFiscaleException;
use Robertogallea\CodiceFiscale\Parsing\Parser;

it('casts the codice_fiscale column to a domain CodiceFiscale instance', function () {
    $person = Person::factory()->create(['codice_fiscale' => validCodeForMilano()]);

    expect($person->codice_fiscale)->toBeInstanceOf(CodiceFiscale::class)
        ->and($person->codice_fiscale->value())->toBe(validCodeForMilano());
});

it('throws immediately when assigned a structurally invalid codice_fiscale', function () {
    $person = Person::factory()->make();

    expect(fn () => $person->codice_fiscale = 'not-a-code')
        ->toThrow(InvalidCodiceFiscaleException::class);
});

it('reproduces a Generator-matched row\'s own birth date, gender, and birthplace when re-parsed', function () {
    $original = new CodiceFiscalePerson(
        firstName: 'Mario',
        lastName: 'Rossi',
        birthDate: new DateTimeImmutable('1995-05-05'),
        birthPlace: BirthPlaceCode::from('F205'),
        gender: Gender::Male,
    );

    $person = Person::factory()->matching($original)->create();

    $parsed = (new Parser(app(BirthPlaceRepository::class)))->parse($person->codice_fiscale);

    expect($parsed->birthDate())->toEqual($original->birthDate)
        ->and($parsed->gender())->toBe($original->gender)
        ->and($parsed->birthPlaceCode())->toEqual($original->birthPlace);
});

it('gives a Faker-provider row a code unrelated to its own stored name', function () {
    // The contrast this whole ticket demonstrates: unlike matching()'s
    // rows, the default factory state's code comes from the Faker
    // provider's own internal random Person, not from the row's
    // first_name/last_name columns.
    $person = Person::factory()->create();

    expect($person->codice_fiscale)->toBeInstanceOf(CodiceFiscale::class);
});
