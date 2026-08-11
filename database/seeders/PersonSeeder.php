<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Robertogallea\CodiceFiscale\Data\DomesticBirthPlace;
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
     * H501 (Roma) is deliberately not used here: verified via a direct
     * sqlite3 query against the real fetched ANPR data
     * (storage/app/codicefiscale/places.sqlite) that its only
     * municipality record there ends 1950-01-01, with no current era
     * captured - a real gap in this dataset, not a bug in
     * knownBirthPlaces() (which only promises a valid code exists, not
     * that every birth date resolves a name against the real
     * repository). F205/F839/L219 below all have a real, currently-open
     * era as of the same check.
     *
     * @return list<CodiceFiscalePerson>
     */
    private function matchedPeople(): array
    {
        return [
            new CodiceFiscalePerson(
                firstName: 'Mario',
                lastName: 'Rossi',
                birthDate: new \DateTimeImmutable('1985-04-12'),
                birthPlace: $this->knownBirthPlace('F205')->code(),
                gender: Gender::Male,
            ),
            new CodiceFiscalePerson(
                firstName: 'Giulia',
                lastName: 'Bianchi',
                birthDate: new \DateTimeImmutable('1990-09-23'),
                birthPlace: $this->knownBirthPlace('F839')->code(),
                gender: Gender::Female,
            ),
            new CodiceFiscalePerson(
                firstName: 'Luca',
                lastName: 'Verdi',
                birthDate: new \DateTimeImmutable('1978-12-01'),
                birthPlace: $this->knownBirthPlace('L219')->code(),
                gender: Gender::Male,
            ),
        ];
    }

    /**
     * Looks up by cadastral code rather than positional index into
     * knownBirthPlaces(), so this seeder can't silently start pointing
     * at the wrong municipality if the package ever reorders that list
     * - it would throw here instead.
     */
    private function knownBirthPlace(string $code): DomesticBirthPlace
    {
        foreach (CodiceFiscaleProvider::knownBirthPlaces() as $birthPlace) {
            if ($birthPlace->code()->value() === $code) {
                return $birthPlace;
            }
        }

        throw new \RuntimeException("CodiceFiscaleProvider::knownBirthPlaces() no longer includes {$code}.");
    }
}
