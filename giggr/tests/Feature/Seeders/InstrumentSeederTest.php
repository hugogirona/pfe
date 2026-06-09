<?php

namespace Tests\Feature\Seeders;

use App\Models\Instrument;
use Database\Seeders\InstrumentSeeder;

it('seeds the instrument catalogue', function () {
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::count())->toBe(29);
});

it('is idempotent', function () {
    $this->seed(InstrumentSeeder::class);
    $count = Instrument::count();
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::count())->toBe($count);
});

it('seeds Guitare', function () {
    $this->seed(InstrumentSeeder::class);

    expect(Instrument::where('slug', 'guitare')->exists())->toBeTrue();
});
