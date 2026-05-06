<?php

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\User;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('mounts with available instruments, genres and types', function () {
    $this->seed([InstrumentSeeder::class, GenreSeeder::class]);
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('parts.announcement.form');

    expect($component->get('availableInstruments'))->not->toBeEmpty()
        ->and($component->get('availableGenres'))->not->toBeEmpty()
        ->and($component->get('availableTypes'))->not->toBeEmpty();
});

it('guest cannot submit the form', function () {
    Livewire::test('parts.announcement.form')
        ->call('save')
        ->assertForbidden();
});

it('title is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

it('title must be at least 5 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'abc')
        ->call('save')
        ->assertHasErrors(['title' => 'min']);
});

it('title cannot exceed 100 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', str_repeat('a', 101))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

it('type is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('type', '')
        ->call('save')
        ->assertHasErrors(['type' => 'required']);
});

it('type must be a valid enum value', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('type', 'invalid')
        ->call('save')
        ->assertHasErrors(['type' => 'in']);
});

it('city_id is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('city_id', null)
        ->call('save')
        ->assertHasErrors(['city_id' => 'required']);
});

it('city_id must reference an existing city', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('city_id', 99999)
        ->call('save')
        ->assertHasErrors(['city_id' => 'exists']);
});

it('description is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('description', '')
        ->call('save')
        ->assertHasErrors(['description' => 'required']);
});

it('description must be at least 20 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('description', 'Trop court.')
        ->call('save')
        ->assertHasErrors(['description' => 'min']);
});

it('description cannot exceed 1000 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('description', str_repeat('a', 1001))
        ->call('save')
        ->assertHasErrors(['description' => 'max']);
});

it('creates an announcement on valid submission', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'search')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->call('save')
        ->assertHasNoErrors();

    expect(Announcement::where('user_id', $user->id)->first())
        ->not->toBeNull()
        ->title->toBe('Cherche bassiste pour trio jazz')
        ->city_id->toBe($city->id)
        ->status->toBe(AnnouncementStatus::Open);
});

it('syncs selected instruments on save', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();
    $instruments = Instrument::factory()->count(2)->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'search')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->set('selectedInstruments', $instruments->pluck('id')->toArray())
        ->call('save');

    expect(Announcement::first()->instruments)->toHaveCount(2);
});

it('syncs selected genres on save', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();
    $genres = Genre::factory()->count(3)->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'search')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->set('selectedGenres', $genres->pluck('id')->toArray())
        ->call('save');

    expect(Announcement::first()->genres)->toHaveCount(3);
});

it('shows success state after save', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'search')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->call('save')
        ->assertSet('success', true);
});

it('dispatches announcement-created with the new id', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'search')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->call('save')
        ->assertDispatched('announcement-created');
});

it('toggleInstrument adds an instrument id', function () {
    $user = User::factory()->create();
    $instrument = Instrument::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->call('toggleInstrument', $instrument->id)
        ->assertSet('selectedInstruments', [$instrument->id]);
});

it('toggleInstrument removes an already-selected instrument id', function () {
    $user = User::factory()->create();
    $instrument = Instrument::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('selectedInstruments', [$instrument->id])
        ->call('toggleInstrument', $instrument->id)
        ->assertSet('selectedInstruments', []);
});

it('toggleGenre adds a genre id', function () {
    $user = User::factory()->create();
    $genre = Genre::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->call('toggleGenre', $genre->id)
        ->assertSet('selectedGenres', [$genre->id]);
});

it('toggleGenre removes an already-selected genre id', function () {
    $user = User::factory()->create();
    $genre = Genre::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('selectedGenres', [$genre->id])
        ->call('toggleGenre', $genre->id)
        ->assertSet('selectedGenres', []);
});

it('close dispatches close-modal', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->call('close')
        ->assertDispatched('close-modal');
});
