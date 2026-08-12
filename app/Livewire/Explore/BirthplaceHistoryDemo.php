<?php

namespace App\Livewire\Explore;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\DomesticBirthPlace;

/**
 * Demonstrates that the birthplace domain correctly models history,
 * using a real comune: G698, verified directly against the real
 * ANPR data (storage/app/codicefiscale/places.sqlite, fetched by
 * codice-fiscale:update-places) to have two genuine identity changes
 * - renamed Piperno to Priverno on 1928-09-09, then reprovinced RM to
 * LT on 1934-10-26 - both mirrored exactly in this app's test fixture
 * (tests/Support/InMemoryBirthPlaceRepository). Also demonstrates
 * existedEver() distinguishing "never existed" from "existed, wrong
 * date" via X999, a code verified absent from both the real
 * ANPR/MAECI data and this app's test fixture.
 */
class BirthplaceHistoryDemo extends Component
{
    private const RENAMED_COMUNE_CODE = 'G698';

    private const NEVER_EXISTED_CODE = 'X999';

    public string $date = '1990-01-01';

    /** @var array{status: string, name: ?string, province: ?string}|null */
    public ?array $historyResult = null;

    /** @var array{status: string, name: ?string, province: ?string}|null */
    public ?array $neverExistedResult = null;

    public function mount(): void
    {
        $this->resolve();
    }

    public function updatedDate(): void
    {
        $this->resolve();
    }

    public function resolve(): void
    {
        if ($this->date === '') {
            // new DateTimeImmutable('') would silently resolve to "now"
            // rather than erroring - treat a blanked date field as "not
            // computable yet" instead of a misleading today-based result.
            $noDateResult = ['status' => 'no_date', 'name' => null, 'province' => null];
            $this->historyResult = $noDateResult;
            $this->neverExistedResult = $noDateResult;

            return;
        }

        $repository = app(BirthPlaceRepository::class);
        $date = new \DateTimeImmutable($this->date);

        $this->historyResult = $this->resolveStatus($repository, self::RENAMED_COMUNE_CODE, $date);
        $this->neverExistedResult = $this->resolveStatus($repository, self::NEVER_EXISTED_CODE, $date);
    }

    public function render(): View
    {
        return view('livewire.explore.birthplace-history-demo');
    }

    /** @return array{status: string, name: ?string, province: ?string} */
    private function resolveStatus(BirthPlaceRepository $repository, string $codeValue, \DateTimeImmutable $date): array
    {
        $code = BirthPlaceCode::from($codeValue);

        if (! $repository->existedEver($code)) {
            return ['status' => 'never_existed', 'name' => null, 'province' => null];
        }

        $place = $repository->find($code, $date);

        if ($place === null) {
            return ['status' => 'not_valid_on_date', 'name' => null, 'province' => null];
        }

        return [
            'status' => 'valid',
            'name' => $place->name(),
            'province' => $place instanceof DomesticBirthPlace ? $place->province() : null,
        ];
    }
}
