<?php

namespace App\Support;

use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;

/**
 * Turns a BirthPlaceRepository::search() call into combobox-ready
 * options, deduped by BirthPlaceCode. Shared by every page with a
 * birthplace combobox (the hero tool's Calculate form and its
 * person-matching disclosure, the CenturyResolver/BirthDateResolver
 * playground, the People-directory create form) - search() is
 * unfiltered by validity and returns most-recent-era first, so a
 * renamed/reprovinced comune's several era-records sharing one code
 * would otherwise appear as visually-identical duplicate options;
 * keeping the first occurrence per code keeps only its current (most
 * recent) era.
 */
final class BirthPlaceOptionSearcher
{
    private const DEFAULT_LIMIT = 10;

    /** @return list<array{code: string, label: string}> */
    public static function search(BirthPlaceRepository $repository, string $query, int $limit = self::DEFAULT_LIMIT): array
    {
        if (trim($query) === '') {
            return [];
        }

        $places = $repository->search($query, limit: $limit);

        $seenCodes = [];
        $options = [];

        foreach ($places as $place) {
            $code = $place->code()->value();

            if (isset($seenCodes[$code])) {
                continue;
            }

            $seenCodes[$code] = true;
            $options[] = ['code' => $code, 'label' => self::label($place)];
        }

        return $options;
    }

    private static function label(BirthPlace $place): string
    {
        return "{$place->name()} ({$place->code()->value()})";
    }
}
