<?php

use App\Support\BirthPlaceOptionSearcher;
use Robertogallea\CodiceFiscale\Contracts\BirthPlace;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Robertogallea\CodiceFiscale\Data\DomesticBirthPlace;
use Tests\Support\InMemoryBirthPlaceRepository;

it('returns an empty list for a blank query', function () {
    expect(BirthPlaceOptionSearcher::search(new InMemoryBirthPlaceRepository, ''))->toBe([]);
});

it('deduplicates era-records sharing a code and labels with the cadastral code', function () {
    $options = BirthPlaceOptionSearcher::search(new InMemoryBirthPlaceRepository, 'milano');

    expect($options)->toHaveCount(1)
        ->and($options[0])->toBe(['code' => 'F205', 'label' => 'Milano (F205)']);
});

it('respects the result limit', function () {
    // Both Roma and (via "narrow" substring in name) other entries could
    // match broad queries in principle; here just confirm a tight limit
    // is honored against a query with more than one match.
    $options = BirthPlaceOptionSearcher::search(new InMemoryBirthPlaceRepository, 'a', limit: 1);

    expect($options)->toHaveCount(1);
});

it('ranks an exact name match ahead of a prefix match ahead of a plain substring match', function () {
    // Purpose-built double (not the shared InMemoryBirthPlaceRepository
    // fixture, which other tests depend on having exact contents) with
    // three "roma"-adjacent names chosen to exercise all three tiers.
    $options = BirthPlaceOptionSearcher::search(new OrderingBirthPlaceRepository, 'roma');

    expect($options)->toBe([
        ['code' => 'H501', 'label' => 'Roma (H501)'],
        ['code' => 'X001', 'label' => 'Romagnano al Monte (X001)'],
        ['code' => 'X002', 'label' => 'Villa di Roma (X002)'],
    ]);
});

it('surfaces an exact cadastral code match even though the code text is not in the name', function () {
    // "F205" would not textually match "Milano" by name at all (the
    // InMemoryBirthPlaceRepository fixture's own normalize() doesn't
    // strip digits, so a substring search for "f205" finds nothing) -
    // this only works via the exact BirthPlaceRepository::find() path.
    $options = BirthPlaceOptionSearcher::search(new InMemoryBirthPlaceRepository, 'F205');

    expect($options)->toBe([
        ['code' => 'F205', 'label' => 'Milano (F205)'],
    ]);
});

it('falls back to ordinary name search when a code-shaped query does not exist', function () {
    // "A100" is syntactically a valid BirthPlaceCode but isn't in the
    // fixture, so find() returns null and there's no name match either.
    $options = BirthPlaceOptionSearcher::search(new InMemoryBirthPlaceRepository, 'A100');

    expect($options)->toBe([]);
});

it('orders same-tier matches alphabetically by name, not by repository/era order', function () {
    // Both "San Zeno" and "San Alberto" are prefix (tier 1) matches for
    // "san"; the double returns Zeno before Alberto, so this only
    // passes if the final ordering re-sorts within the tier by name.
    $options = BirthPlaceOptionSearcher::search(new OrderingBirthPlaceRepository, 'san');

    expect($options)->toBe([
        ['code' => 'X011', 'label' => 'San Alberto (X011)'],
        ['code' => 'X010', 'label' => 'San Zeno (X010)'],
    ]);
});

/**
 * A minimal BirthPlaceRepository double purpose-built for ordering tests -
 * kept local to this file rather than added to the shared
 * InMemoryBirthPlaceRepository fixture, to avoid perturbing the other
 * tests that rely on that fixture's exact contents.
 */
final class OrderingBirthPlaceRepository implements BirthPlaceRepository
{
    /** @var list<BirthPlace> */
    private array $places;

    public function __construct()
    {
        $epoch = new DateTimeImmutable('1861-01-01');

        $this->places = [
            new DomesticBirthPlace(BirthPlaceCode::from('X002'), 'Villa di Roma', 'XX', '000002', $epoch),
            new DomesticBirthPlace(BirthPlaceCode::from('X001'), 'Romagnano al Monte', 'SA', '000001', $epoch),
            new DomesticBirthPlace(BirthPlaceCode::from('H501'), 'Roma', 'RM', '058091', $epoch),
            new DomesticBirthPlace(BirthPlaceCode::from('X010'), 'San Zeno', 'XX', '000010', $epoch),
            new DomesticBirthPlace(BirthPlaceCode::from('X011'), 'San Alberto', 'XX', '000011', $epoch),
        ];
    }

    public function find(BirthPlaceCode $code, ?DateTimeImmutable $on = null): ?BirthPlace
    {
        foreach ($this->places as $place) {
            if ($place->code()->equals($code)) {
                return $place;
            }
        }

        return null;
    }

    public function existedEver(BirthPlaceCode $code): bool
    {
        return $this->find($code) !== null;
    }

    public function search(string $name, ?DateTimeImmutable $on = null, ?int $limit = null): array
    {
        $needle = mb_strtolower($name);

        $results = array_values(array_filter(
            $this->places,
            fn (BirthPlace $place) => str_contains(mb_strtolower($place->name()), $needle),
        ));

        return $limit === null ? $results : array_slice($results, 0, $limit);
    }
}
