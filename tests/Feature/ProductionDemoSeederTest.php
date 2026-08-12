<?php

use App\Models\Person;
use Database\Seeders\ProductionDemoSeeder;

it('seeds deterministic demo people without Faker', function () {
    app(ProductionDemoSeeder::class)->run();
    app(ProductionDemoSeeder::class)->run();

    expect(Person::query()->count())->toBe(3)
        ->and(Person::query()->pluck('first_name')->all())->toEqualCanonicalizing(['Mario', 'Giulia', 'Luca']);
});
