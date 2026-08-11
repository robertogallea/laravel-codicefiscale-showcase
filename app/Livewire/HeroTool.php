<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Generator;

class HeroTool extends Component
{
    private const BIRTH_PLACE_RESULTS_LIMIT = 10;

    public string $firstName = '';

    public string $lastName = '';

    public string $birthDate = '';

    public string $gender = 'M';

    public string $birthPlaceQuery = '';

    public ?string $birthPlaceCode = null;

    public ?string $generatedCode = null;

    /**
     * Options for the birthplace combobox, deduped by BirthPlaceCode.
     *
     * search() is unfiltered by validity and returns most-recent-era
     * first, so a renamed/reprovinced comune's several era-records
     * sharing one code would otherwise appear as visually-identical
     * duplicate options - keeping the first occurrence per code keeps
     * only its current (most recent) era.
     *
     * @return list<array{code: string, label: string}>
     */
    #[Computed]
    public function birthPlaceOptions(): array
    {
        if (trim($this->birthPlaceQuery) === '') {
            return [];
        }

        $repository = app(BirthPlaceRepository::class);
        $places = $repository->search($this->birthPlaceQuery, limit: self::BIRTH_PLACE_RESULTS_LIMIT);

        $seenCodes = [];
        $options = [];

        foreach ($places as $place) {
            $code = $place->code()->value();

            if (isset($seenCodes[$code])) {
                continue;
            }

            $seenCodes[$code] = true;
            $options[] = ['code' => $code, 'label' => $this->optionLabel($place)];
        }

        return $options;
    }

    public function generate(): void
    {
        $this->generatedCode = null;

        $data = $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'birthDate' => 'required|date',
            'gender' => 'required|in:M,F',
            // Mirrors BirthPlaceCode's own (protected) structural pattern -
            // catching a malformed value here, before it ever reaches
            // BirthPlaceCode::from(), turns what would otherwise be an
            // uncaught InvalidBirthPlaceCodeException into a normal,
            // translated field error.
            'birthPlaceCode' => ['required', 'regex:/^[A-Z][0-9]{3}$/'],
        ], [
            'birthPlaceCode.required' => __('messages.hero.calculate.birth_place_unresolved'),
            'birthPlaceCode.regex' => __('messages.hero.calculate.birth_place_unresolved'),
        ]);

        $person = new Person(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            birthDate: new \DateTimeImmutable($data['birthDate']),
            birthPlace: BirthPlaceCode::from($data['birthPlaceCode']),
            gender: Gender::from($data['gender']),
        );

        $this->generatedCode = (new Generator)->generate($person)->value();
    }

    public function render(): View
    {
        return view('livewire.hero-tool');
    }

    private function optionLabel(BirthPlace $place): string
    {
        return "{$place->name()} ({$place->code()->value()})";
    }
}
