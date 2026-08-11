<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Robertogallea\CodiceFiscale\Data\Person as CodiceFiscalePerson;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Laravel\Faker\CodiceFiscaleProvider;

class PersonSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::factory()->count(20)->create();

        foreach ($this->matchedPeople() as $person) {
            Person::factory()->matching($person)->create();
        }
    }

    /**
     * A handful of Generator-matched rows, each built from its own
     * explicit Person value object rather than the Faker provider's
     * internal one - see PersonFactory::matching(). Draws birthplaces
     * from CodiceFiscaleProvider::knownBirthPlaces() (ADR-0007's
     * hand-picked, always-real, no-network-required list) so seeding
     * never depends on codice-fiscale:update-places having run first.
     *
     * @return list<CodiceFiscalePerson>
     */
    private function matchedPeople(): array
    {
        $birthPlaces = CodiceFiscaleProvider::knownBirthPlaces();

        // Index 0 (H501/Roma) is deliberately skipped: verified via a
        // direct sqlite3 query against the real fetched ANPR data
        // (storage/app/codicefiscale/places.sqlite) that its only
        // municipality record there ends 1950-01-01, with no current
        // era captured - a real gap in this dataset, not a bug in
        // knownBirthPlaces() (which only promises a valid code exists,
        // not that every birth date resolves a name against the real
        // repository). Indices 1-3 all have a real, currently-open era.
        return [
            new CodiceFiscalePerson(
                firstName: 'Mario',
                lastName: 'Rossi',
                birthDate: new \DateTimeImmutable('1985-04-12'),
                birthPlace: $birthPlaces[1]->code(),
                gender: Gender::Male,
            ),
            new CodiceFiscalePerson(
                firstName: 'Giulia',
                lastName: 'Bianchi',
                birthDate: new \DateTimeImmutable('1990-09-23'),
                birthPlace: $birthPlaces[2]->code(),
                gender: Gender::Female,
            ),
            new CodiceFiscalePerson(
                firstName: 'Luca',
                lastName: 'Verdi',
                birthDate: new \DateTimeImmutable('1978-12-01'),
                birthPlace: $birthPlaces[3]->code(),
                gender: Gender::Male,
            ),
        ];
    }
}
