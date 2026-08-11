<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Generator;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// No RefreshDatabase: the Browser suite's one test never touches Eloquent,
// only the fixture BirthPlaceRepository binding TestCase already provides.
pest()->extend(TestCase::class)
    ->in('Browser');

/*
|--------------------------------------------------------------------------
| Shared fixture data
|--------------------------------------------------------------------------
*/

/**
 * A real, generated codice fiscale for Mario Rossi (born 1995-05-05,
 * Male, Milano/F205) - the fixture repository's Milano record, reused
 * across hero-tool Feature tests that need a known-good decoded code.
 */
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
