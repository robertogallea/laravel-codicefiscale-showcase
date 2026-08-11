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
use Robertogallea\CodiceFiscale\Data\PartialPerson;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Enums\PersonField;
use Robertogallea\CodiceFiscale\Enums\ValidationError;
use Robertogallea\CodiceFiscale\Generation\Generator;
use Robertogallea\CodiceFiscale\Matching\Matcher;
use Robertogallea\CodiceFiscale\Matching\MatchResult;
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

    public bool $checkAgainstPerson = false;

    public string $matchFirstName = '';

    public string $matchLastName = '';

    public string $matchBirthDate = '';

    public string $matchBirthPlaceQuery = '';

    public ?string $matchBirthPlaceCode = null;

    public string $matchGender = '';

    /** @var array{first_name: string, last_name: string, birth_date: string, birth_place: string, gender: string}|null */
    public ?array $matchStatuses = null;

    public function mount(): void
    {
        if (trim($this->decodeInput) !== '') {
            $this->switchToDecode($this->decodeInput);
        }
    }

    /**
     * Options for the Calculate birthplace combobox. See
     * searchBirthPlaces() for the deduplication this relies on.
     *
     * @return list<array{code: string, label: string}>
     */
    #[Computed]
    public function birthPlaceOptions(): array
    {
        return $this->searchBirthPlaces($this->birthPlaceQuery);
    }

    /**
     * Options for the person-matching disclosure's own birthplace
     * combobox - kept as a separate computed property from
     * birthPlaceOptions() because it's driven by a different bound
     * query property, not a different search.
     *
     * @return list<array{code: string, label: string}>
     */
    #[Computed]
    public function matchBirthPlaceOptions(): array
    {
        return $this->searchBirthPlaces($this->matchBirthPlaceQuery);
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
            'birthPlaceCode.required' => __('messages.hero.fields.birth_place_unresolved'),
            'birthPlaceCode.regex' => __('messages.hero.fields.birth_place_unresolved'),
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

        $repository = $this->repository();
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

    /**
     * Checks the decoded code against whichever person fields were
     * actually filled in, via PartialPerson - an omitted field is
     * skipped rather than forced into a mismatch, per Matcher's own
     * contract.
     */
    public function checkMatch(): void
    {
        $this->matchStatuses = null;

        if ($this->decodedCode === null) {
            return;
        }

        $data = $this->validate([
            'matchBirthDate' => 'nullable|date',
            // Same rationale as generate()'s birthPlaceCode rule: catches
            // a malformed value before it reaches BirthPlaceCode::from().
            'matchBirthPlaceCode' => ['nullable', 'regex:/^[A-Z][0-9]{3}$/'],
        ], [
            'matchBirthPlaceCode.regex' => __('messages.hero.fields.birth_place_unresolved'),
        ]);

        $person = new PartialPerson(
            firstName: $this->matchFirstName !== '' ? $this->matchFirstName : null,
            lastName: $this->matchLastName !== '' ? $this->matchLastName : null,
            // Laravel's "nullable" rule does not coerce '' to null, and
            // `new DateTimeImmutable('')` resolves to "now" rather than
            // throwing - checking !== '' (not !== null) is what actually
            // treats a blank date input as skipped.
            birthDate: $data['matchBirthDate'] !== '' ? new \DateTimeImmutable($data['matchBirthDate']) : null,
            birthPlace: $data['matchBirthPlaceCode'] !== null ? BirthPlaceCode::from($data['matchBirthPlaceCode']) : null,
            gender: $this->matchGender !== '' ? Gender::from($this->matchGender) : null,
        );

        $repository = $this->repository();
        $result = (new Matcher(new Parser($repository)))->match(CodiceFiscale::from($this->decodedCode), $person);

        $this->matchStatuses = [
            'first_name' => $this->statusFor(PersonField::FirstName, $result),
            'last_name' => $this->statusFor(PersonField::LastName, $result),
            'birth_date' => $this->statusFor(PersonField::BirthDate, $result),
            'birth_place' => $this->statusFor(PersonField::BirthPlace, $result),
            'gender' => $this->statusFor(PersonField::Gender, $result),
        ];
    }

    public function render(): View
    {
        return view('livewire.hero-tool');
    }

    /**
     * @return list<array{code: string, label: string}>
     */
    private function searchBirthPlaces(string $query): array
    {
        if (trim($query) === '') {
            return [];
        }

        $repository = $this->repository();
        $places = $repository->search($query, limit: self::BIRTH_PLACE_RESULTS_LIMIT);

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

    private function repository(): BirthPlaceRepository
    {
        return app(BirthPlaceRepository::class);
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

    private function statusFor(PersonField $field, MatchResult $result): string
    {
        return match (true) {
            in_array($field, $result->matched(), true) => 'matched',
            in_array($field, $result->mismatched(), true) => 'mismatched',
            default => 'skipped',
        };
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
