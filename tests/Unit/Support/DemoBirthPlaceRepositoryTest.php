<?php

use App\Support\BirthPlaces\DemoBirthPlaceRepository;
use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;

beforeEach(function () {
    $this->repository = new DemoBirthPlaceRepository;
});

it('finds a fictional birthplace by its demo-only code', function () {
    $place = $this->repository->find(BirthPlaceCode::from('X001'));

    expect($place)->not->toBeNull()
        ->and($place->name())->toBe('Atlantis')
        ->and($place->country()->value())->toBe('ATL');
});

it('returns null and false for a code that only exists in the real repository', function () {
    // F205 (Milano) is real ANPR data - never present in the demo repository.
    expect($this->repository->find(BirthPlaceCode::from('F205')))->toBeNull()
        ->and($this->repository->existedEver(BirthPlaceCode::from('F205')))->toBeFalse();
});

it('distinguishes never-existed from valid via existedEver', function () {
    expect($this->repository->existedEver(BirthPlaceCode::from('X001')))->toBeTrue()
        ->and($this->repository->existedEver(BirthPlaceCode::from('X999')))->toBeFalse();
});

it('searches its fictional places by case-insensitive substring match', function () {
    $results = $this->repository->search('narn');

    expect($results)->toHaveCount(1)
        ->and($results[0]->name())->toBe('Narnia');
});
