<?php

namespace App\Livewire\People;

use App\Models\Person;
use App\Support\BirthPlaceOptionSearcher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Laravel\Rules\CodiceFiscaleRule;

class PeopleCreate extends Component
{
    public string $firstName = '';

    public string $lastName = '';

    public string $birthDate = '';

    public string $gender = 'M';

    public string $birthPlaceQuery = '';

    public ?string $birthPlaceCode = null;

    public string $codiceFiscale = '';

    /** @return list<array{code: string, label: string}> */
    #[Computed]
    public function birthPlaceOptions(): array
    {
        return BirthPlaceOptionSearcher::search(app(BirthPlaceRepository::class), $this->birthPlaceQuery);
    }

    /**
     * Validated by CodiceFiscaleRule::make()->matching() - the fluent,
     * cross-field-aware equivalent of the plain codice_fiscale rule
     * string demonstrated on the Explore validation-rule page. Every
     * field name passed to matching() below is a Livewire property
     * name, not a translated label: those names double as both the
     * lookup key CodiceFiscaleRule reads from the validated data and
     * the :field placeholder in its mismatch message, so they can't be
     * independently prettified without breaking the lookup - see
     * ADR-0010 on this rule's own package, which places that naming
     * decision on the consuming app.
     */
    public function create(): void
    {
        $validated = $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'birthDate' => 'required|date',
            'gender' => 'required|in:M,F',
            // Mirrors HeroTool::generate()'s own birthPlaceCode rule -
            // catches a malformed value before it reaches
            // BirthPlaceCode::from() inside the rule below.
            'birthPlaceCode' => ['required', 'regex:/^[A-Z][0-9]{3}$/'],
            'codiceFiscale' => [
                'required',
                CodiceFiscaleRule::make()->matching(
                    firstName: 'firstName',
                    lastName: 'lastName',
                    birthDate: 'birthDate',
                    birthPlace: 'birthPlaceCode',
                    gender: 'gender',
                ),
            ],
        ], [
            'birthPlaceCode.required' => __('messages.hero.fields.birth_place_unresolved'),
            'birthPlaceCode.regex' => __('messages.hero.fields.birth_place_unresolved'),
        ], [
            'codiceFiscale' => __('messages.people.fields.codice_fiscale'),
        ]);

        $person = Person::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'codice_fiscale' => $validated['codiceFiscale'],
        ]);

        $this->redirectRoute('people.show', $person);
    }

    public function render(): View
    {
        return view('livewire.people.people-create');
    }
}
