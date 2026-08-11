<?php

namespace App\Support\BirthPlaces;

use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\CountryCode;
use Robertogallea\CodiceFiscale\Data\ForeignBirthPlace;

/**
 * The "Demo repository" per this app's glossary (see CONTEXT.md): a
 * hand-authored, entirely fictional BirthPlaceRepository implementation,
 * used only by the standalone Repository-swap Explore page to prove
 * Contracts\BirthPlaceRepository is genuinely swappable. Never bound
 * into the container, never used by the hero tool - the real
 * CompositeBirthPlaceRepository is always what generates/decodes a
 * real-looking code. Codes use the unused "X" prefix (real domestic
 * and foreign codes never start with X) so they can never collide
 * with genuine ANPR/MAECI data.
 */
final class DemoBirthPlaceRepository implements BirthPlaceRepository
{
    /** @var list<BirthPlace> */
    private array $places;

    public function __construct()
    {
        $epoch = new \DateTimeImmutable('0001-01-01');

        $this->places = [
            new ForeignBirthPlace(BirthPlaceCode::from('X001'), 'Atlantis', CountryCode::from('ATL'), $epoch),
            new ForeignBirthPlace(BirthPlaceCode::from('X002'), 'Narnia', CountryCode::from('NAR'), $epoch),
            new ForeignBirthPlace(BirthPlaceCode::from('X003'), 'Wakanda', CountryCode::from('WKD'), $epoch),
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
        $needle = mb_strtolower($name);

        $results = array_values(array_filter(
            $this->places,
            fn (BirthPlace $place) => str_contains(mb_strtolower($place->name()), $needle)
                && ($on === null || $place->wasValidOn($on)),
        ));

        return $limit === null ? $results : array_slice($results, 0, $limit);
    }
}
