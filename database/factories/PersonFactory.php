<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Robertogallea\CodiceFiscale\Data\Person as CodiceFiscalePerson;
use Robertogallea\CodiceFiscale\Generation\Generator;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Default state: a Faker-provider row. fake()->codiceFiscale()
     * builds its own random Person internally (see
     * CodiceFiscaleProvider::randomPerson()) - its code has no
     * relationship to this row's own first_name/last_name, unlike
     * matching()'s rows below.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'codice_fiscale' => fake()->codiceFiscale(),
        ];
    }

    /**
     * A Generator-matched row: the stored codice_fiscale is generated
     * directly from $person via Generator::generate(), and
     * first_name/last_name are taken from that same $person - so
     * re-parsing this row's own codice_fiscale reproduces $person's
     * birth date, gender, and birthplace exactly.
     */
    public function matching(CodiceFiscalePerson $person): static
    {
        return $this->state(fn (): array => [
            'first_name' => $person->firstName,
            'last_name' => $person->lastName,
            'codice_fiscale' => (new Generator)->generate($person)->value(),
        ]);
    }
}
