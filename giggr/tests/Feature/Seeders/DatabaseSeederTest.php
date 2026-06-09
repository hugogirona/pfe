<?php

namespace Tests\Feature\Seeders;

use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;

it('seeds all three lookup catalogues via DatabaseSeeder', function () {
    $this->seed();

    expect(City::count())->toBeGreaterThan(2700)
        ->and(Instrument::count())->toBe(29)
        ->and(Genre::count())->toBe(35);
});

it('is idempotent end-to-end', function () {
    $this->seed();
    $cities = City::count();
    $instruments = Instrument::count();
    $genres = Genre::count();
    $this->seed();

    expect(City::count())->toBe($cities)
        ->and(Instrument::count())->toBe($instruments)
        ->and(Genre::count())->toBe($genres);
});
