<?php

namespace Tests\Feature\Models;

use App\Models\Announcement;
use App\Models\City;
use App\Models\Profile;
use Illuminate\Database\QueryException;

it('can create a city with all fields', function () {
    $city = City::create([
        'name' => 'Liège',
        'name_alt' => 'Luik',
        'slug' => 'liege-4000',
        'country' => 'BE',
        'postal_code' => '4000',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
        'searchable' => 'liege luik 4000',
    ]);

    expect($city->fresh())
        ->name->toBe('Liège')
        ->name_alt->toBe('Luik')
        ->slug->toBe('liege-4000')
        ->country->toBe('BE')
        ->postal_code->toBe('4000')
        ->latitude->toEqual(50.6326)
        ->longitude->toEqual(5.5797)
        ->searchable->toBe('liege luik 4000');
});

it('defaults country to BE when not provided', function () {
    $city = City::create([
        'name' => 'Bruxelles',
        'slug' => 'bruxelles-1000',
        'postal_code' => '1000',
        'searchable' => 'bruxelles brussel 1000',
    ]);

    expect($city->fresh()->country)->toBe('BE');
});

it('allows null name_alt, latitude and longitude', function () {
    $city = City::create([
        'name' => 'Maldegem',
        'slug' => 'maldegem-9990',
        'postal_code' => '9990',
        'searchable' => 'maldegem 9990',
    ]);

    expect($city->fresh())
        ->name_alt->toBeNull()
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('enforces a unique slug', function () {
    City::create([
        'name' => 'Liège', 'slug' => 'liege-4000', 'postal_code' => '4000', 'searchable' => 'liege 4000',
    ]);

    expect(fn () => City::create([
        'name' => 'Liège', 'slug' => 'liege-4000', 'postal_code' => '4000', 'searchable' => 'liege 4000',
    ]))->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $city = City::factory()->create();

    expect($city)->toBeInstanceOf(City::class)
        ->and($city->slug)->not->toBeEmpty()
        ->and($city->postal_code)->not->toBeEmpty()
        ->and($city->searchable)->not->toBeEmpty();
});

it('makeSearchable normalizes name + alt + postal into a slug', function () {
    expect(City::makeSearchable('Liège', 'Luik', '4000'))->toBe('liege luik 4000')
        ->and(City::makeSearchable('Maldegem', null, '9990'))->toBe('maldegem  9990');
});

it('exposes display_name accessor as "name (postal_code)"', function () {
    $city = City::factory()->create([
        'name' => 'Liège', 'postal_code' => '4000',
    ]);

    expect($city->display_name)->toBe('Liège (4000)');
});

it('has many profiles', function () {
    $city = City::factory()->create();
    Profile::factory()->count(2)->create(['city_id' => $city->id]);

    expect($city->profiles)->toHaveCount(2);
});

it('has many announcements', function () {
    $city = City::factory()->create();
    Announcement::factory()->count(3)->create(['city_id' => $city->id]);

    expect($city->announcements)->toHaveCount(3);
});
