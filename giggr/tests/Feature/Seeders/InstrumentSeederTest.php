<?php

namespace Tests\Feature\Seeders;

use App\Models\Instrument;
use Database\Seeders\InstrumentSeeder;

it('seeds 9 instruments', function () {
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::count())->toBe(9);
});

it('is idempotent', function () {
    $this->seed(InstrumentSeeder::class);
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::count())->toBe(9);
});

it('seeds Guitare', function () {
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::where('slug', 'guitare')->exists())->toBeTrue();
});
