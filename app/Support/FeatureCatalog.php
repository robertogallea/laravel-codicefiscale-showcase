<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * The showcase's fixed catalog of feature pages, in a deliberate order:
 * the hero tool's own three modes, then the Explore playgrounds, then
 * the real People-directory integration. This single ordered list is
 * shared by the homepage index and every feature page's prev/next
 * rail, so both always agree on what "next" means, and each entry
 * carries the short "quadro" code (echoing a real tax-form section
 * code, e.g. Modello Redditi's Quadro RC) shown as that page's eyebrow.
 */
class FeatureCatalog
{
    /**
     * @return list<array{key: string, routeName: string, routeParams: array<string, mixed>, headingKey: string, descriptionKey: string, quadro: string}>
     */
    public static function entries(): array
    {
        return [
            [
                'key' => 'calculate',
                'routeName' => 'home',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.calculate.heading',
                'descriptionKey' => 'messages.home.features.calculate.description',
                'quadro' => 'CF·1',
            ],
            [
                'key' => 'decode',
                'routeName' => 'home',
                'routeParams' => ['mode' => 'decode'],
                'headingKey' => 'messages.home.features.decode.heading',
                'descriptionKey' => 'messages.home.features.decode.description',
                'quadro' => 'CF·2',
            ],
            [
                'key' => 'person_matching',
                'routeName' => 'home',
                'routeParams' => ['mode' => 'decode', 'match' => 1],
                'headingKey' => 'messages.home.features.person_matching.heading',
                'descriptionKey' => 'messages.home.features.person_matching.description',
                'quadro' => 'CF·3',
            ],
            [
                'key' => 'omocodia',
                'routeName' => 'explore.omocodia',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.omocodia.heading',
                'descriptionKey' => 'messages.home.features.omocodia.description',
                'quadro' => 'OM',
            ],
            [
                'key' => 'repository_swap',
                'routeName' => 'explore.repositories',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.repository_swap.heading',
                'descriptionKey' => 'messages.home.features.repository_swap.description',
                'quadro' => 'RS',
            ],
            [
                'key' => 'birth_date_resolver',
                'routeName' => 'explore.birth-date-resolver',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.birth_date_resolver.heading',
                'descriptionKey' => 'messages.home.features.birth_date_resolver.description',
                'quadro' => 'BD',
            ],
            [
                'key' => 'birthplace_history',
                'routeName' => 'explore.birthplace-history',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.birthplace_history.heading',
                'descriptionKey' => 'messages.home.features.birthplace_history.description',
                'quadro' => 'BH',
            ],
            [
                'key' => 'validation_rule',
                'routeName' => 'explore.validation-rule',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.validation_rule.heading',
                'descriptionKey' => 'messages.home.features.validation_rule.description',
                'quadro' => 'VR',
            ],
            [
                'key' => 'people',
                'routeName' => 'people.index',
                'routeParams' => [],
                'headingKey' => 'messages.home.features.people.heading',
                'descriptionKey' => 'messages.home.features.people.description',
                'quadro' => 'PD',
            ],
        ];
    }

    /**
     * @return array{key: string, routeName: string, routeParams: array<string, mixed>, headingKey: string, descriptionKey: string, quadro: string, url: string}
     */
    public static function find(string $key): array
    {
        $entry = self::collection()->firstWhere('key', $key);

        abort_if($entry === null, 404);

        return [...$entry, 'url' => route($entry['routeName'], $entry['routeParams'])];
    }

    /**
     * The entry immediately before and after the given feature, wrapping
     * around at either end of the catalog, plus this feature's 1-based
     * position for the "3 of 9" indicator.
     *
     * @return array{prev: array{key: string, routeName: string, routeParams: array<string, mixed>, headingKey: string, descriptionKey: string, quadro: string, url: string}, next: array{key: string, routeName: string, routeParams: array<string, mixed>, headingKey: string, descriptionKey: string, quadro: string, url: string}, position: int, total: int}
     */
    public static function neighbors(string $key): array
    {
        $entries = self::entries();
        $index = self::collection()->search(fn (array $entry): bool => $entry['key'] === $key);

        abort_if($index === false, 404);

        $total = count($entries);
        $prev = $entries[($index - 1 + $total) % $total];
        $next = $entries[($index + 1) % $total];

        return [
            'prev' => [...$prev, 'url' => route($prev['routeName'], $prev['routeParams'])],
            'next' => [...$next, 'url' => route($next['routeName'], $next['routeParams'])],
            'position' => $index + 1,
            'total' => $total,
        ];
    }

    /**
     * @return Collection<int, array{key: string, routeName: string, routeParams: array<string, mixed>, headingKey: string, descriptionKey: string, quadro: string}>
     */
    private static function collection(): Collection
    {
        return collect(self::entries());
    }
}
