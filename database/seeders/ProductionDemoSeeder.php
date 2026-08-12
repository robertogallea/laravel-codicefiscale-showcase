<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person as CodiceFiscalePerson;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Generator;

class ProductionDemoSeeder extends Seeder
{
    /**
     * Seed a small, deterministic set of people without the development-only Faker dependency.
     */
    public function run(): void
    {
        foreach ($this->people() as $person) {
            Person::query()->firstOrCreate(
                ['codice_fiscale' => (new Generator)->generate($person)->value()],
                [
                    'first_name' => $person->firstName,
                    'last_name' => $person->lastName,
                ],
            );
        }
    }

    /** @return list<CodiceFiscalePerson> */
    private function people(): array
    {
        return [
            new CodiceFiscalePerson(
                firstName: 'Mario',
                lastName: 'Rossi',
                birthDate: new \DateTimeImmutable('1985-04-12'),
                birthPlace: BirthPlaceCode::from('F205'),
                gender: Gender::Male,
            ),
            new CodiceFiscalePerson(
                firstName: 'Giulia',
                lastName: 'Bianchi',
                birthDate: new \DateTimeImmutable('1990-09-23'),
                birthPlace: BirthPlaceCode::from('F839'),
                gender: Gender::Female,
            ),
            new CodiceFiscalePerson(
                firstName: 'Luca',
                lastName: 'Verdi',
                birthDate: new \DateTimeImmutable('1978-12-01'),
                birthPlace: BirthPlaceCode::from('L219'),
                gender: Gender::Male,
            ),
        ];
    }
}
