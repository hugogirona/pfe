<?php

use App\Models\City;
use Illuminate\Support\Str;
use Livewire\Livewire;

function locality(string $name, string $postal, ?string $alt = null): City
{
    return City::factory()->create([
        'name' => $name,
        'name_alt' => $alt,
        'slug' => Str::slug($name).'-'.$postal,
        'postal_code' => $postal,
        'searchable' => City::makeSearchable($name, $alt, $postal),
    ]);
}

it('shows nothing when query is empty', function () {
    Livewire::test('parts.form.locality-picker')
        ->assertSet('results', [])
        ->set('query', '')
        ->assertSet('results', []);
});

it('filters localities by partial canonical name', function () {
    locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'lieg')
        ->assertSee('Liège (4000)');
});

it('matches accent-insensitively', function () {
    locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'liege')
        ->assertSee('Liège (4000)');
});

it('matches via the FR alias when name is in NL', function () {
    locality('Antwerpen', '2000', 'Anvers');

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'anvers')
        ->assertSee('Antwerpen (2000)');
});

it('matches via the NL alias when name is in FR', function () {
    locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'luik')
        ->assertSee('Liège (4000)');
});

it('matches by postal code', function () {
    locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker')
        ->set('query', '4000')
        ->assertSee('Liège (4000)');
});

it('caps results at 8', function () {
    foreach (range(1, 10) as $i) {
        locality("Anville{$i}", str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT));
    }

    $component = Livewire::test('parts.form.locality-picker')
        ->set('query', 'anv');

    expect(count($component->get('results')))->toBe(8);
});

it('selecting a result sets cityId and replaces the query with the display name', function () {
    $liege = locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'lieg')
        ->call('selectCity', $liege->id)
        ->assertSet('cityId', $liege->id)
        ->assertSet('query', 'Liège (4000)')
        ->assertSet('results', []);
});

it('renders the preselected city display name on mount', function () {
    $liege = locality('Liège', '4000', 'Luik');

    Livewire::test('parts.form.locality-picker', ['cityId' => $liege->id])
        ->assertSet('query', 'Liège (4000)');
});

it('selectFromCoords picks the nearest city when coords fall close enough', function () {
    $liege = City::factory()->create([
        'name' => 'Liège',
        'slug' => 'liege-4000',
        'postal_code' => '4000',
        'searchable' => 'liege 4000',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
    ]);
    City::factory()->create([
        'name' => 'Bruxelles',
        'slug' => 'bruxelles-1000',
        'postal_code' => '1000',
        'searchable' => 'bruxelles 1000',
        'latitude' => 50.8466,
        'longitude' => 4.3528,
    ]);

    Livewire::test('parts.form.locality-picker')
        ->call('selectFromCoords', 50.6326, 5.5797)
        ->assertSet('cityId', $liege->id)
        ->assertSet('query', 'Liège (4000)')
        ->assertSet('tooFar', false);
});

it('selectFromCoords flags tooFar and leaves the selection untouched when no city is within range', function () {
    $liege = City::factory()->create([
        'name' => 'Liège',
        'slug' => 'liege-4000',
        'postal_code' => '4000',
        'searchable' => 'liege 4000',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
    ]);

    Livewire::test('parts.form.locality-picker', ['cityId' => $liege->id])
        ->call('selectFromCoords', 40.7128, -74.0060) // New York
        ->assertSet('cityId', $liege->id)
        ->assertSet('query', 'Liège (4000)')
        ->assertSet('tooFar', true);
});

it('selectFromCoords clears a previous tooFar flag once a city is found', function () {
    $liege = City::factory()->create([
        'name' => 'Liège',
        'slug' => 'liege-4000',
        'postal_code' => '4000',
        'searchable' => 'liege 4000',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
    ]);

    Livewire::test('parts.form.locality-picker')
        ->set('tooFar', true)
        ->call('selectFromCoords', 50.6326, 5.5797)
        ->assertSet('tooFar', false)
        ->assertSet('cityId', $liege->id);
});
