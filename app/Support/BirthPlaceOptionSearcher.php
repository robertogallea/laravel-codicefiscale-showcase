<?php

namespace App\Support;

use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Support\PlaceNameNormalizer;

/**
 * Turns a BirthPlaceRepository::search() call into combobox-ready
 * options, ranked and deduped by BirthPlaceCode. Shared by every page
 * with a birthplace combobox (the hero tool's Calculate form and its
 * person-matching disclosure, the CenturyResolver/BirthDateResolver
 * playground, the People-directory create form).
 *
 * Results are ranked by match quality rather than search()'s raw
 * era-recency order: an exact name/cadastral-code match first, then a
 * name that starts with the query, then any other substring match.
 * Code matching is exact-only: search() has no prefix-code search
 * capability, only find() for a complete, valid code.
 *
 * The dedupe-by-code step (keep first occurrence per code) uses
 * era-recency to pick a winner - "best tier, then most recent era" -
 * so a renamed/reprovinced comune's several era-records sharing one
 * code still collapse to a single, current-era option. Once that
 * filtering settles which options qualify, the final display order
 * within each tier is alphabetical by name, not era-recency.
 */
final class BirthPlaceOptionSearcher
{
    private const DEFAULT_LIMIT = 10;

    /**
     * How many raw name-search candidates to fetch, relative to the
     * final option limit, before ranking and truncating. Bounded
     * headroom for exact/prefix matches to surface even when they
     * aren't the most recent era - not an exhaustive search.
     */
    private const SEARCH_POOL_MULTIPLIER = 5;

    /** @return list<array{code: string, label: string}> */
    public static function search(BirthPlaceRepository $repository, string $query, int $limit = self::DEFAULT_LIMIT): array
    {
        $trimmedQuery = trim($query);

        if ($trimmedQuery === '') {
            return [];
        }

        $normalizer = new PlaceNameNormalizer;
        $normalizedQuery = $normalizer->normalize($trimmedQuery);

        /** @var list<array{0: int, 1: BirthPlace}> */
        $ranked = [];

        if ($exactCodeMatch = self::findByExactCode($repository, $trimmedQuery)) {
            $ranked[] = [0, $exactCodeMatch];
        }

        foreach ($repository->search($query, limit: $limit * self::SEARCH_POOL_MULTIPLIER) as $place) {
            $ranked[] = [self::nameMatchTier($place, $normalizedQuery, $normalizer), $place];
        }

        usort($ranked, fn (array $a, array $b) => $a[0] <=> $b[0]);

        $seenCodes = [];
        $selected = [];

        foreach ($ranked as $entry) {
            $code = $entry[1]->code()->value();

            if (isset($seenCodes[$code])) {
                continue;
            }

            $seenCodes[$code] = true;
            $selected[] = $entry;

            if (count($selected) === $limit) {
                break;
            }
        }

        usort($selected, fn (array $a, array $b) => $a[0] <=> $b[0]
            ?: $normalizer->normalize($a[1]->name()) <=> $normalizer->normalize($b[1]->name()));

        return array_map(
            fn (array $entry) => ['code' => $entry[1]->code()->value(), 'label' => self::label($entry[1])],
            $selected,
        );
    }

    private static function findByExactCode(BirthPlaceRepository $repository, string $query): ?BirthPlace
    {
        $code = BirthPlaceCode::tryFrom($query);

        return $code === null ? null : $repository->find($code);
    }

    private static function nameMatchTier(BirthPlace $place, string $normalizedQuery, PlaceNameNormalizer $normalizer): int
    {
        $normalizedName = $normalizer->normalize($place->name());

        return match (true) {
            $normalizedName === $normalizedQuery => 0,
            str_starts_with($normalizedName, $normalizedQuery) => 1,
            default => 2,
        };
    }

    private static function label(BirthPlace $place): string
    {
        return "{$place->name()} ({$place->code()->value()})";
    }
}
