<?php

namespace Tests\Feature\Seeders;

use App\Models\City;
use Database\Seeders\CitySeeder;

it('seeds 10 Belgian cities', function () {
    $this->seed(CitySeeder::class);

    expect(City::count())->toBe(10);
});

it('is idempotent — running twice does not duplicate rows', function () {
    $this->seed(CitySeeder::class);
    $this->seed(CitySeeder::class);

    expect(City::count())->toBe(10);
});

it('seeds Liège with real coordinates', function () {
    $this->seed(CitySeeder::class);

    $liege = City::where('slug', 'liege')->first();

    expect($liege)->not->toBeNull()
        ->and($liege->name)->toBe('Liège')
        ->and($liege->country)->toBe('BE')
        ->and($liege->latitude)->toBeGreaterThan(50.0)
        ->and($liege->longitude)->toBeGreaterThan(5.0);
});
