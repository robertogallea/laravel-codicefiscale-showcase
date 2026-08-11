<?php

namespace App\Livewire\Explore;

use App\Support\BirthPlaces\DemoBirthPlaceRepository;
use App\Support\ValidationErrorPresenter;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Robertogallea\CodiceFiscale\CodiceFiscale;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\Person;
use Robertogallea\CodiceFiscale\Enums\Gender;
use Robertogallea\CodiceFiscale\Generation\Generator;
use Robertogallea\CodiceFiscale\Parsing\Parser;
use Robertogallea\CodiceFiscale\Validation\Validator;

/**
 * Resolves one fixed demonstration code against two different
 * BirthPlaceRepository bindings - the "Real repository" (the same
 * one the hero tool always uses) and the "Demo repository" (fictional
 * places, used only here) - proving the contract is genuinely
 * swappable. See CONTEXT.md for both terms.
 */
class RepositorySwapDemo extends Component
{
    /**
     * X001 only exists in the Demo repository - resolving it real vs.
     * demo is what makes the swap visible, not just differently-worded.
     */
    private const DEMONSTRATION_BIRTH_PLACE_CODE = 'X001';

    public string $code;

    public string $repository = 'demo';

    public ?bool $valid = null;

    public ?string $errorMessage = null;

    /** @var array{gender: string, birth_date: ?string, birth_place: ?string}|null */
    public ?array $details = null;

    public function mount(): void
    {
        $person = new Person(
            firstName: 'Mario',
            lastName: 'Rossi',
            birthDate: new \DateTimeImmutable('1995-05-05'),
            birthPlace: BirthPlaceCode::from(self::DEMONSTRATION_BIRTH_PLACE_CODE),
            gender: Gender::Male,
        );

        $this->code = (new Generator)->generate($person)->value();

        $this->resolve();
    }

    /**
     * Livewire's naming convention auto-calls this when $repository
     * changes via wire:model.live - re-resolving the same fixed code
     * under the newly selected repository.
     */
    public function updatedRepository(): void
    {
        $this->resolve();
    }

    public function resolve(): void
    {
        $this->valid = null;
        $this->errorMessage = null;
        $this->details = null;

        $repository = $this->currentRepository();
        $result = (new Validator($repository))->validate($this->code);

        if (! $result->valid()) {
            $this->valid = false;
            $this->errorMessage = ValidationErrorPresenter::describe($result->errors());

            return;
        }

        $parsed = (new Parser($repository))->parse(CodiceFiscale::from($this->code));

        $this->valid = true;
        $this->details = [
            'gender' => $parsed->gender()->value,
            'birth_date' => $parsed->birthDate()?->format('Y-m-d'),
            'birth_place' => $parsed->birthPlace()?->name(),
        ];
    }

    public function render(): View
    {
        return view('livewire.explore.repository-swap-demo');
    }

    /**
     * The swap being demonstrated is at the Contracts\BirthPlaceRepository
     * level - Validator/Parser only ever depend on the interface, so
     * they work identically whether the instance came from the
     * container or a plain `new`. The Demo repository is deliberately
     * never bound into the container (unlike the Real one, resolved
     * via app()): that's what guarantees it can never leak into the
     * hero tool by accident, matching CONTEXT.md's "never used by the
     * hero tool" definition of the Demo repository.
     */
    private function currentRepository(): BirthPlaceRepository
    {
        return $this->repository === 'demo'
            ? new DemoBirthPlaceRepository
            : app(BirthPlaceRepository::class);
    }
}
