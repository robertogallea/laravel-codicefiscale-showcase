<?php

use Robertogallea\CodiceFiscale\Data\BirthPlaceCode;
use Tests\Support\InMemoryBirthPlaceRepository;

beforeEach(function () {
    $this->repository = new InMemoryBirthPlaceRepository;
});

it('finds a known domestic birthplace by code', function () {
    $place = $this->repository->find(BirthPlaceCode::from('F205'));

    expect($place)->not->toBeNull()
        ->and($place->name())->toBe('Milano')
        ->and($place->province())->toBe('MI');
});

it('finds a known foreign birthplace by code', function () {
    $place = $this->repository->find(BirthPlaceCode::from('Z404'));

    expect($place)->not->toBeNull()
        ->and($place->name())->toBe('Stati Uniti d\'America')
        ->and($place->country()->value())->toBe('USA');
});

it('returns null for a code that never existed', function () {
    expect($this->repository->find(BirthPlaceCode::from('A999')))->toBeNull();
});

it('distinguishes never-existed from valid-wrong-date via existedEver', function () {
    expect($this->repository->existedEver(BirthPlaceCode::from('F205')))->toBeTrue()
        ->and($this->repository->existedEver(BirthPlaceCode::from('A999')))->toBeFalse();
});

it('searches by case/accent-insensitive substring match', function () {
    $results = $this->repository->search('roma');

    expect($results)->toHaveCount(1)
        ->and($results[0]->name())->toBe('Roma');
});

it('returns an empty list when nothing matches a search', function () {
    expect($this->repository->search('Nowhereville'))->toBe([]);
});
