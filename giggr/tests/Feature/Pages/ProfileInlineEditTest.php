<?php

use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('owner can save bio', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->set('bio', 'Une bio suffisamment longue pour passer la validation.')
        ->call('saveBio')
        ->assertDispatched('bio-saved');

    expect($profile->fresh()->bio)->toBe('Une bio suffisamment longue pour passer la validation.');
});

it('saveBio requires at least 10 characters', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->set('bio', 'Court')
        ->call('saveBio')
        ->assertHasErrors(['bio']);
});

it('non-owner cannot save bio', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $originalBio = $profile->bio;
    $visitor = User::factory()->create();

    try {
        Livewire::actingAs($visitor)
            ->test('pages::profile.index', ['id' => $profile->id])
            ->set('bio', 'Tentative de modification du profil de quelqu\'un d\'autre.')
            ->call('saveBio');
    } catch (Throwable) {
    }

    expect($profile->fresh()->bio)->toBe($originalBio);
});

it('non-owner cannot save instruments', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $originalIds = $profile->fresh()->instruments->pluck('id')->sort()->values()->toArray();
    $visitor = User::factory()->create();

    try {
        Livewire::actingAs($visitor)
            ->test('pages::profile.index', ['id' => $profile->id])
            ->call('saveInstruments');
    } catch (Throwable) {
    }

    expect($profile->fresh()->instruments->pluck('id')->sort()->values()->toArray())
        ->toBe($originalIds);
});

it('non-owner cannot save genres', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $originalIds = $profile->fresh()->genres->pluck('id')->sort()->values()->toArray();
    $visitor = User::factory()->create();

    try {
        Livewire::actingAs($visitor)
            ->test('pages::profile.index', ['id' => $profile->id])
            ->call('saveGenres');
    } catch (Throwable) {
    }

    expect($profile->fresh()->genres->pluck('id')->sort()->values()->toArray())
        ->toBe($originalIds);
});

it('saveInstruments rejects non-existent IDs', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $originalIds = $profile->fresh()->instruments->pluck('id')->sort()->values()->toArray();

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->set('selectedInstruments', [999999])
        ->call('saveInstruments')
        ->assertHasErrors(['selectedInstruments.*']);

    expect($profile->fresh()->instruments->pluck('id')->sort()->values()->toArray())
        ->toBe($originalIds);
});

it('saveGenres rejects non-existent IDs', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $originalIds = $profile->fresh()->genres->pluck('id')->sort()->values()->toArray();

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->set('selectedGenres', [999999])
        ->call('saveGenres')
        ->assertHasErrors(['selectedGenres.*']);

    expect($profile->fresh()->genres->pluck('id')->sort()->values()->toArray())
        ->toBe($originalIds);
});

it('owner can toggle and save instruments', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $profile->instruments()->detach();

    $instrumentId = Instrument::first()->id;

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->call('toggleInstrument', $instrumentId)
        ->call('saveInstruments')
        ->assertDispatched('instruments-saved');

    expect($profile->fresh()->instruments->pluck('id')->contains($instrumentId))->toBeTrue();
});

it('owner can toggle and save genres', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $profile->genres()->detach();

    $genreId = Genre::first()->id;

    Livewire::actingAs($profile->user)
        ->test('pages::profile.index', ['id' => $profile->id])
        ->call('toggleGenre', $genreId)
        ->call('saveGenres')
        ->assertDispatched('genres-saved');

    expect($profile->fresh()->genres->pluck('id')->contains($genreId))->toBeTrue();
});
