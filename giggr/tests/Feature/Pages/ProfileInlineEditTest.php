<?php

use App\Enums\ProfileStatus;
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
        ->test('pages::profile.show', ['profile' => $profile])
        ->set('bio', 'Une bio suffisamment longue pour passer la validation.')
        ->call('saveBio')
        ->assertDispatched('bio-saved');

    expect($profile->fresh()->bio)->toBe('Une bio suffisamment longue pour passer la validation.');
});

it('saveBio requires at least 10 characters', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
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
            ->test('pages::profile.show', ['profile' => $profile])
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
            ->test('pages::profile.show', ['profile' => $profile])
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
            ->test('pages::profile.show', ['profile' => $profile])
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
        ->test('pages::profile.show', ['profile' => $profile])
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
        ->test('pages::profile.show', ['profile' => $profile])
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
        ->test('pages::profile.show', ['profile' => $profile])
        ->call('toggleInstrument', $instrumentId)
        ->call('saveInstruments')
        ->assertDispatched('instruments-saved');

    expect($profile->fresh()->instruments->pluck('id')->contains($instrumentId))->toBeTrue();
});

it('owner can save status to any ProfileStatus case', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create(['status' => ProfileStatus::LookingForBand]);

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
        ->set('selectedStatus', ProfileStatus::Teaching->value)
        ->call('saveStatus')
        ->assertDispatched('status-saved');

    expect($profile->fresh()->status)->toBe(ProfileStatus::Teaching);
});

it('does not offer the automatic newcomer state in the status selector', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $component = Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile]);

    expect(array_column($component->get('allStatuses'), 'value'))
        ->not->toContain(ProfileStatus::Newcomer->value)
        ->toContain(ProfileStatus::LookingForBand->value);
});

it('saveStatus rejects the automatic newcomer state', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create(['status' => ProfileStatus::LookingForBand]);

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
        ->set('selectedStatus', ProfileStatus::Newcomer->value)
        ->call('saveStatus')
        ->assertHasErrors(['selectedStatus']);

    expect($profile->fresh()->status)->toBe(ProfileStatus::LookingForBand);
});

it('saveStatus rejects values outside the ProfileStatus enum', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create(['status' => ProfileStatus::LookingForBand]);

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
        ->set('selectedStatus', 'rockstar_overlord')
        ->call('saveStatus')
        ->assertHasErrors(['selectedStatus']);

    expect($profile->fresh()->status)->toBe(ProfileStatus::LookingForBand);
});

it('saveStatus accepts the new value as an argument', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create(['status' => ProfileStatus::LookingForBand]);

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
        ->call('saveStatus', ProfileStatus::OpenToCollab->value)
        ->assertSet('selectedStatus', ProfileStatus::OpenToCollab->value)
        ->assertDispatched('status-saved');

    expect($profile->fresh()->status)->toBe(ProfileStatus::OpenToCollab);
});

it('saveStatus preserves the followers and followed counts on the loaded profile', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $owner = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $followed = User::factory()->withProfile()->create();
    $follower->follow($owner->profile);
    $owner->follow($followed->profile);

    $component = Livewire::actingAs($owner)
        ->test('pages::profile.show', ['profile' => $owner->profile])
        ->set('selectedStatus', ProfileStatus::Teaching->value)
        ->call('saveStatus');

    $profile = $component->get('profile');
    expect($profile->followers_count)->toBe(1)
        ->and($profile->followed_count)->toBe(1);
});

it('non-owner cannot save status', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create(['status' => ProfileStatus::LookingForBand]);
    $visitor = User::factory()->create();

    try {
        Livewire::actingAs($visitor)
            ->test('pages::profile.show', ['profile' => $profile])
            ->set('selectedStatus', ProfileStatus::Teaching->value)
            ->call('saveStatus');
    } catch (Throwable) {
    }

    expect($profile->fresh()->status)->toBe(ProfileStatus::LookingForBand);
});

it('owner can toggle and save genres', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $profile->genres()->detach();

    $genreId = Genre::first()->id;

    Livewire::actingAs($profile->user)
        ->test('pages::profile.show', ['profile' => $profile])
        ->call('toggleGenre', $genreId)
        ->call('saveGenres')
        ->assertDispatched('genres-saved');

    expect($profile->fresh()->genres->pluck('id')->contains($genreId))->toBeTrue();
});
