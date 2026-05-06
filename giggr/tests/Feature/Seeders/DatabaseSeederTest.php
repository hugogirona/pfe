<?php

namespace Tests\Feature\Seeders;

use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;

it('seeds all three lookup catalogues via DatabaseSeeder', function () {
    $this->seed();

    expect(City::count())->toBeGreaterThan(2700)
        ->and(Instrument::count())->toBe(9)
        ->and(Genre::count())->toBe(12);
});

it('is idempotent end-to-end', function () {
    $this->seed();
    $this->seed();

    expect(City::count())->toBeGreaterThan(2700)
        ->and(Instrument::count())->toBe(9)
        ->and(Genre::count())->toBe(12);
});
