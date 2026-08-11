<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Robertogallea\CodiceFiscale\CodiceFiscale;
use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Enums\ValidationError;
use Robertogallea\CodiceFiscale\Generation\Generator;
use Robertogallea\CodiceFiscale\Parsing\Parser;
use Robertogallea\CodiceFiscale\Validation\Validator;

class HeroTool extends Component
{
    private const BIRTH_PLACE_RESULTS_LIMIT = 10;

    public string $mode = 'calculate';

    public string $firstName = '';

    public string $lastName = '';

    public string $birthDate = '';

    public string $gender = 'M';

    public string $birthPlaceQuery = '';

    public ?string $birthPlaceCode = null;

    public ?string $generatedCode = null;

    #[Url(as: 'cf')]
    public string $decodeInput = '';

    public ?bool $decodeValid = null;

    public ?string $decodeErrorMessage = null;

    public ?string $decodedCode = null;

    /** @var array{gender: string, birth_date: ?string, birth_place: ?string, is_omocodia: bool}|null */
    public ?array $decodedDetails = null;

    public function mount(): void
    {
        if (trim($this->decodeInput) !== '') {
            $this->switchToDecode($this->decodeInput);
        }
    }

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

    /**
     * Jumps from a Calculate result straight into Decode, pre-filled
     * and already showing the parsed result - the action that makes
     * the generated code shareable via the URL-bound decodeInput.
     */
    public function verifyGeneratedCode(): void
    {
        $this->switchToDecode((string) $this->generatedCode);
    }

    public function decode(): void
    {
        $this->decodeValid = null;
        $this->decodeErrorMessage = null;
        $this->decodedCode = null;
        $this->decodedDetails = null;

        $code = trim($this->decodeInput);

        if ($code === '') {
            return;
        }

        $repository = app(BirthPlaceRepository::class);
        $result = (new Validator($repository))->validate($code);

        if (! $result->valid()) {
            $this->decodeValid = false;
            $this->decodeErrorMessage = $this->friendlyErrorMessage($result->errors());

            return;
        }

        $cf = CodiceFiscale::from($code);
        $parsed = (new Parser($repository))->parse($cf);

        $this->decodeValid = true;
        $this->decodedCode = $cf->value();
        $this->decodedDetails = [
            'gender' => $parsed->gender()->value,
            'birth_date' => $parsed->birthDate()?->format('Y-m-d'),
            'birth_place' => $parsed->birthPlace()?->name(),
            'is_omocodia' => $parsed->isOmocodia(),
        ];
    }

    public function render(): View
    {
        return view('livewire.hero-tool');
    }

    private function switchToDecode(string $code): void
    {
        $this->decodeInput = $code;
        $this->mode = 'decode';
        $this->decode();
    }

    private function optionLabel(BirthPlace $place): string
    {
        return "{$place->name()} ({$place->code()->value()})";
    }

    /** @param  list<ValidationError>  $errors */
    private function friendlyErrorMessage(array $errors): string
    {
        $messages = array_map(
            fn (ValidationError $error) => __('messages.decode.errors.'.$error->value),
            $errors,
        );

        return implode(' ', $messages);
    }
}
