<?php

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('redirects guests to login', function () {
    $this->get(route('profile.setup'))
        ->assertRedirectToRoute('login');
});

it('renders for authenticated user with empty profile', function () {
    $user = User::factory()->create();
    Profile::create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('profile.setup'))
        ->assertOk();
});

it('redirects to own profile when already setup', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('profile.setup'))
        ->assertRedirect(route('profile', ['id' => $profile->id]));
});

it('saves profile data and redirects to own profile', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::profile.setup')
        ->set('bio', 'Je joue de la guitare depuis dix ans.')
        ->set('experienceYears', 10)
        ->call('save')
        ->assertRedirect(route('profile', ['id' => $profile->id]));

    expect($profile->fresh())
        ->bio->toBe('Je joue de la guitare depuis dix ans.')
        ->experience_years->toBe(10);
});

it('skip redirects to explore', function () {
    $user = User::factory()->create();
    Profile::create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::profile.setup')
        ->call('skip')
        ->assertRedirect(route('explore'));
});

it('requires a bio of at least 10 characters', function () {
    $user = User::factory()->create();
    Profile::create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::profile.setup')
        ->set('bio', 'Court')
        ->call('save')
        ->assertHasErrors(['bio']);
});
