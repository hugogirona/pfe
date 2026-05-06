<?php

use App\Models\City;
use Database\Seeders\CitySeeder;
use Livewire\Livewire;

it('shows nothing when query is empty', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->assertSet('results', [])
        ->set('query', '')
        ->assertSet('results', []);
});

it('filters localities by partial canonical name', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'lieg')
        ->assertSee('Liège (4000)');
});

it('matches accent-insensitively', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'liege')
        ->assertSee('Liège (4000)');
});

it('matches via the FR alias when name is in NL', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'anvers')
        ->assertSee('Antwerpen (2000)');
});

it('matches via the NL alias when name is in FR', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'luik')
        ->assertSee('Liège (4000)');
});

it('matches by postal code', function () {
    $this->seed(CitySeeder::class);

    Livewire::test('parts.form.locality-picker')
        ->set('query', '4000')
        ->assertSee('Liège (4000)');
});

it('caps results at 8', function () {
    $this->seed(CitySeeder::class);

    $component = Livewire::test('parts.form.locality-picker')
        ->set('query', 'a');

    expect(count($component->get('results')))->toBeLessThanOrEqual(8);
});

it('selecting a result sets cityId and replaces the query with the display name', function () {
    $this->seed(CitySeeder::class);
    $liege = City::where('postal_code', '4000')->where('name', 'Liège')->first();

    Livewire::test('parts.form.locality-picker')
        ->set('query', 'lieg')
        ->call('selectCity', $liege->id)
        ->assertSet('cityId', $liege->id)
        ->assertSet('query', 'Liège (4000)')
        ->assertSet('results', []);
});

it('renders the preselected city display name on mount', function () {
    $this->seed(CitySeeder::class);
    $liege = City::where('postal_code', '4000')->where('name', 'Liège')->first();

    Livewire::test('parts.form.locality-picker', ['cityId' => $liege->id])
        ->assertSet('query', 'Liège (4000)');
});
