<?php

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
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
        ->set('type', 'musician_wanted')
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
        ->set('type', 'musician_wanted')
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
        ->set('type', 'musician_wanted')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->set('selectedGenres', $genres->pluck('id')->toArray())
        ->call('save');

    expect(Announcement::first()->genres)->toHaveCount(3);
});

it('redirects to the new announcement page after creating it', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->set('title', 'Cherche bassiste pour trio jazz')
        ->set('type', 'musician_wanted')
        ->set('city_id', $city->id)
        ->set('description', 'Groupe de jazz en quête d\'un bassiste expérimenté pour sessions régulières.')
        ->call('save')
        ->assertRedirect(route('announcement', ['id' => Announcement::first()->id]));
});

it('shows the success state after updating an existing announcement', function () {
    $owner = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();

    Livewire::actingAs($owner)
        ->test('parts.announcement.form', ['model_id' => (string) $announcement->id])
        ->set('title', 'Updated title for this announcement')
        ->call('save')
        ->assertSet('success', true);
});

it('close dispatches close-modal', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->call('close')
        ->assertDispatched('close-modal');
});

it('mounts with prefilled fields when given an owned announcement id', function () {
    $this->seed([InstrumentSeeder::class, GenreSeeder::class]);
    $owner = User::factory()->create();
    $city = City::factory()->create();
    $instruments = Instrument::factory()->count(2)->create();
    $genres = Genre::factory()->count(1)->create();

    $announcement = Announcement::factory()->for($owner)->create([
        'city_id' => $city->id,
        'title' => 'Trio jazz cherche bassiste',
        'description' => 'Trois saxos en quête d\'une basse pour des sessions du soir.',
        'type' => AnnouncementType::MusicianWanted,
    ]);
    $announcement->instruments()->sync($instruments->pluck('id')->all());
    $announcement->genres()->sync($genres->pluck('id')->all());

    Livewire::actingAs($owner)
        ->test('parts.announcement.form', ['model_id' => (string) $announcement->id])
        ->assertSet('title', 'Trio jazz cherche bassiste')
        ->assertSet('type', 'musician_wanted')
        ->assertSet('city_id', $city->id)
        ->assertSet('description', 'Trois saxos en quête d\'une basse pour des sessions du soir.')
        ->assertSet('selectedInstruments', $instruments->pluck('id')->all())
        ->assertSet('selectedGenres', $genres->pluck('id')->all());
});

it('refuses to mount in edit mode for a non-owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();

    Livewire::actingAs($other)
        ->test('parts.announcement.form', ['model_id' => (string) $announcement->id])
        ->assertForbidden();
});

it('save updates the existing announcement when in edit mode', function () {
    $this->seed([InstrumentSeeder::class, GenreSeeder::class]);
    $owner = User::factory()->create();
    $city = City::factory()->create();
    $newCity = City::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create([
        'city_id' => $city->id,
        'title' => 'Original title here',
        'description' => 'Original description with enough characters to validate.',
    ]);

    Livewire::actingAs($owner)
        ->test('parts.announcement.form', ['model_id' => (string) $announcement->id])
        ->set('title', 'Updated title goes here')
        ->set('city_id', $newCity->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('announcement-updated');

    expect($announcement->fresh())
        ->title->toBe('Updated title goes here')
        ->city_id->toBe($newCity->id)
        ->and(Announcement::count())->toBe(1);
});

it('delete removes the announcement and dispatches the event', function () {
    $owner = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();

    Livewire::actingAs($owner)
        ->test('parts.announcement.form', ['model_id' => (string) $announcement->id])
        ->call('delete')
        ->assertDispatched('announcement-deleted')
        ->assertDispatched('close-modal');

    expect(Announcement::find($announcement->id))->toBeNull();
});

it('delete refuses to act when not in edit mode', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.announcement.form')
        ->call('delete')
        ->assertStatus(404);
});

it('delete refuses to act for a non-owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();
    Livewire::actingAs($other)
        ->test('parts.announcement.form')
        ->set('model_id', (string) $announcement->id)
        ->call('delete')
        ->assertForbidden();
});
