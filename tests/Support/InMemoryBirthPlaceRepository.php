<?php

namespace Tests\Support;

use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\CountryCode;
use Robertogallea\CodiceFiscale\Data\DomesticBirthPlace;
use Robertogallea\CodiceFiscale\Data\ForeignBirthPlace;

/**
 * A small, fixed set of era-records used across this app's test suite,
 * bound in place of the real ANPR/MAECI-backed repository so tests never
 * depend on network access or the real government data.
 */
final class InMemoryBirthPlaceRepository implements BirthPlaceRepository
{
    /** @var list<BirthPlace> */
    private array $places;

    public function __construct()
    {
        $epoch = new \DateTimeImmutable('1861-01-01');
        $reprovincedOn = new \DateTimeImmutable('1900-01-01');

        $this->places = [
            // Two era-records sharing one code, same name, different
            // province - a synthetic stand-in for the real trap
            // described in .ai/rules/support.md (a comune that changed
            // province keeps its BirthPlaceCode across both eras).
            // Not a real historical claim about Milano.
            new DomesticBirthPlace(BirthPlaceCode::from('F205'), 'Milano', 'XX', '015146', $epoch, $reprovincedOn),
            new DomesticBirthPlace(BirthPlaceCode::from('F205'), 'Milano', 'MI', '015146', $reprovincedOn),
            new DomesticBirthPlace(BirthPlaceCode::from('H501'), 'Roma', 'RM', '058091', $epoch),
            new ForeignBirthPlace(BirthPlaceCode::from('Z404'), 'Stati Uniti d\'America', CountryCode::from('USA'), $epoch),
        ];
    }

    public function find(BirthPlaceCode $code, ?\DateTimeImmutable $on = null): ?BirthPlace
    {
        $on ??= new \DateTimeImmutable;

        foreach ($this->places as $place) {
            if ($place->code()->equals($code) && $place->wasValidOn($on)) {
                return $place;
            }
        }

        return null;
    }

    public function existedEver(BirthPlaceCode $code): bool
    {
        foreach ($this->places as $place) {
            if ($place->code()->equals($code)) {
                return true;
            }
        }

        return false;
    }

    public function search(string $name, ?\DateTimeImmutable $on = null, ?int $limit = null): array
    {
        $needle = $this->normalize($name);

        $results = array_values(array_filter(
            $this->places,
            fn (BirthPlace $place) => str_contains($this->normalize($place->name()), $needle)
                && ($on === null || $place->wasValidOn($on)),
        ));

        usort($results, fn (BirthPlace $a, BirthPlace $b) => $b->validFrom() <=> $a->validFrom());

        return $limit === null ? $results : array_slice($results, 0, $limit);
    }

    private function normalize(string $value): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;

        return mb_strtolower($transliterated);
    }
}
