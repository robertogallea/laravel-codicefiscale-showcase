<?php

use App\Support\BirthPlaceOptionSearcher;
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
