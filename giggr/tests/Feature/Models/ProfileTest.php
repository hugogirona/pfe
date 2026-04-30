<?php

use App\Enums\ProfileStatus;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use App\Models\User;

it('can be created with a user', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($profile->fresh())->toBeInstanceOf(Profile::class)
        ->and($profile->user_id)->toBe($user->id);
});

it('has a default status of looking_for_band', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($profile->fresh()->status)->toBe(ProfileStatus::LookingForBand);
});

it('casts status to ProfileStatus enum', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id, 'status' => ProfileStatus::Teaching]);

    expect($profile->fresh()->status)->toBe(ProfileStatus::Teaching);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($profile->user)->toBeInstanceOf(User::class)
        ->and($profile->user->id)->toBe($user->id);
});

it('allows nullable city', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($profile->city)->toBeNull();
});

it('belongs to a city when city_id is set', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();
    $profile = Profile::create(['user_id' => $user->id, 'city_id' => $city->id]);

    expect($profile->city)->toBeInstanceOf(City::class)
        ->and($profile->city->id)->toBe($city->id);
});

it('user has one profile', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($user->profile)->toBeInstanceOf(Profile::class)
        ->and($user->profile->id)->toBe($profile->id);
});

it('computes age from birth_date', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'birth_date' => now()->subYears(25)->toDateString(),
    ]);

    expect($profile->age)->toBe(25);
});

it('returns null age when birth_date is null', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    expect($profile->age)->toBeNull();
});

it('syncs instruments via pivot', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);
    $instruments = Instrument::factory()->count(2)->create();

    $profile->instruments()->sync($instruments->pluck('id'));

    expect($profile->instruments)->toHaveCount(2);
});

it('syncs genres via pivot', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);
    $genres = Genre::factory()->count(2)->create();

    $profile->genres()->sync($genres->pluck('id'));

    expect($profile->genres)->toHaveCount(2);
});

it('is excluded from default query when soft-deleted', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    $profile->delete();

    expect(Profile::find($profile->id))->toBeNull()
        ->and(Profile::withTrashed()->find($profile->id))->not->toBeNull();
});

it('exposes a working factory', function () {
    $profile = Profile::factory()->create();

    expect($profile)->toBeInstanceOf(Profile::class)
        ->and($profile->user_id)->not->toBeNull();
});
