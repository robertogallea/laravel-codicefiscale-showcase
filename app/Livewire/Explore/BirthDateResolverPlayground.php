<?php

namespace App\Livewire\Explore;

use App\Support\BirthPlaceOptionSearcher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthDateResolutionContext;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Parsing\BirthDate\DefaultBirthDateResolver;

/**
 * Makes the codice fiscale's two-digit birth-year ambiguity, and the
 * swappable BirthDateResolver extension point that resolves it,
 * tangible - built directly against BirthDateResolutionContext and
 * DefaultBirthDateResolver (both public API) rather than a full
 * generated code, since the resolver is the thing being explored.
 */
class BirthDateResolverPlayground extends Component
{
    private const RESOLVABLE_PROPERTIES = ['month', 'day', 'twoDigitYear', 'maxAge', 'birthPlaceCode'];

    public int $month = 5;

    public int $day = 20;

    public string $twoDigitYear = '26';

    public int $maxAge = 120;

    public string $birthPlaceQuery = '';

    public ?string $birthPlaceCode = 'F205';

    /** @var array{0: int, 1: int}|null */
    public ?array $possibleYears = null;

    public ?string $resolvedDate = null;

    public function mount(): void
    {
        $this->resolve();
    }

    public function updated(string $property): void
    {
        if (in_array($property, self::RESOLVABLE_PROPERTIES, true)) {
            $this->resolve();
        }
    }

    #[Computed]
    public function birthPlaceOptions(): array
    {
        return BirthPlaceOptionSearcher::search(app(BirthPlaceRepository::class), $this->birthPlaceQuery);
    }

    public function resolve(): void
    {
        $twoDigitYear = max(0, min(99, (int) $this->twoDigitYear));

        // possibleBirthYears()'s own documented contract: both raw
        // century readings, regardless of calendar or age plausibility.
        $this->possibleYears = [1900 + $twoDigitYear, 2000 + $twoDigitYear];

        if ($this->birthPlaceCode === null) {
            $this->resolvedDate = null;

            return;
        }

        $candidates = array_values(array_filter(array_map(
            fn (int $year) => checkdate($this->month, $this->day, $year)
                ? new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $this->month, $this->day))
                : null,
            $this->possibleYears,
        )));

        $context = new BirthDateResolutionContext(
            candidates: $candidates,
            referenceDate: new \DateTimeImmutable('today'),
            birthPlaceCode: BirthPlaceCode::from($this->birthPlaceCode),
            birthPlaceRepository: app(BirthPlaceRepository::class),
        );

        $resolved = (new DefaultBirthDateResolver($this->maxAge))->resolve($context);

        $this->resolvedDate = $resolved?->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.explore.birth-date-resolver-playground');
    }
}
