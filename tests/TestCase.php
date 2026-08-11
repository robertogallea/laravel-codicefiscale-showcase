<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Tests\Support\InMemoryBirthPlaceRepository;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(BirthPlaceRepository::class, InMemoryBirthPlaceRepository::class);
    }
}
