<?php

namespace Tests\Feature\Seeders;

use App\Models\City;
use Database\Seeders\CitySeeder;

it('seeds all Belgian localities from the bundled CSV', function () {
    $this->seed(CitySeeder::class);

    expect(City::count())->toBeGreaterThan(2700);
});

it('is idempotent — running twice does not duplicate rows', function () {
    $this->seed(CitySeeder::class);
    $first = City::count();
    $this->seed(CitySeeder::class);

    expect(City::count())->toBe($first);
});

it('seeds Bruxelles 1000 with real coordinates and Brussel as alt name', function () {
    $this->seed(CitySeeder::class);

    $bxl = City::where('postal_code', '1000')->where('name', 'Bruxelles')->first();

    expect($bxl)->not->toBeNull()
        ->and($bxl->name_alt)->toBe('Brussel')
        ->and($bxl->country)->toBe('BE')
        ->and($bxl->latitude)->toBeGreaterThan(50.0)
        ->and($bxl->longitude)->toBeGreaterThan(4.0);
});

it('seeds Antwerpen 2000 with Anvers as alt name', function () {
    $this->seed(CitySeeder::class);

    $anvers = City::where('postal_code', '2000')->where('name', 'Antwerpen')->first();

    expect($anvers)->not->toBeNull()
        ->and($anvers->name_alt)->toBe('Anvers');
});

it('seeds Liège 4000 with Luik as alt name', function () {
    $this->seed(CitySeeder::class);

    $liege = City::where('postal_code', '4000')->where('name', 'Liège')->first();

    expect($liege)->not->toBeNull()
        ->and($liege->name_alt)->toBe('Luik');
});

it('searchable column contains both names slugified', function () {
    $this->seed(CitySeeder::class);

    $row = City::where('postal_code', '2000')->where('name', 'Antwerpen')->first();

    expect($row->searchable)->toContain('antwerpen')
        ->and($row->searchable)->toContain('anvers')
        ->and($row->searchable)->toContain('2000');
});

it('localities without an alias have null name_alt', function () {
    $this->seed(CitySeeder::class);

    $maldegem = City::where('postal_code', '9990')->where('name', 'Maldegem')->first();

    expect($maldegem)->not->toBeNull()
        ->and($maldegem->name_alt)->toBeNull();
});
