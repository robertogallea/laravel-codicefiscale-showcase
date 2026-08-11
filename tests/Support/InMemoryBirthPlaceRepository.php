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
            // Exists, but only from 1990 onward - a code that's valid
            // today yet not on an earlier birth date, distinguishing
            // "never existed" (X999 - genuinely unassigned in the real
            // ANPR/MAECI data too, verified directly against it; A999
            // looks unassigned only by accident of this fixture's small
            // size - it's real data, Borghetto Santo Spirito) from
            // "existed, wrong date".
            new ForeignBirthPlace(BirthPlaceCode::from('Z999'), 'Nuova Nazione', CountryCode::from('ZZZ'), new \DateTimeImmutable('1990-01-01')),
            // The mirror image of Z999: valid only long ago, ceased to
            // exist (merged/abolished) before today - needed to exercise
            // DefaultBirthDateResolver's birthplace-history tiebreak,
            // which can only ever prefer an OLDER of two otherwise-
            // plausible candidates when a birthplace is valid at exactly
            // that older date and not the younger one.
            new DomesticBirthPlace(BirthPlaceCode::from('Y001'), 'Comune Storico', 'ZZ', '999999', $epoch, new \DateTimeImmutable('1950-01-01')),
            // G698: a real comune (verified directly against the real
            // ANPR data at storage/app/codicefiscale/places.sqlite,
            // fetched by codice-fiscale:update-places) with two genuine
            // identity changes sharing one code - renamed Piperno to
            // Priverno on 1928-09-09, then reprovinced RM to LT on
            // 1934-10-26. Real dates, names, provinces, and istat
            // codes for all three eras (collapsing only the several
            // same-name-same-province micro-boundary date churns the
            // real data also has, which aren't identity changes),
            // mirrored here so the Birthplace-history Explore page's
            // tests don't depend on network access or the real data.
            new DomesticBirthPlace(BirthPlaceCode::from('G698'), 'Piperno', 'RM', '058929', new \DateTimeImmutable('1871-01-15'), new \DateTimeImmutable('1928-09-09')),
            new DomesticBirthPlace(BirthPlaceCode::from('G698'), 'Priverno', 'RM', '058929', new \DateTimeImmutable('1928-09-09'), new \DateTimeImmutable('1934-10-26')),
            new DomesticBirthPlace(BirthPlaceCode::from('G698'), 'Priverno', 'LT', '059019', new \DateTimeImmutable('1934-10-26')),
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
