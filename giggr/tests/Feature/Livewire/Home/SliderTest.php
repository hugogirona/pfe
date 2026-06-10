<?php

use App\Models\Announcement;
use App\Models\User;
use Livewire\Livewire;

it('renders the profiles slider with profiles', function () {
    User::factory()->withProfile(['avatar_path' => 'avatars/test'])->create();

    Livewire::test('parts.home.slider', ['type' => 'profiles'])
        ->assertSee(__('home.profiles_title'));
});

it('renders the announcements slider with active listings', function () {
    $owner = User::factory()->withProfile()->create();
    Announcement::factory()->for($owner)->create();

    Livewire::test('parts.home.slider', ['type' => 'announcements'])
        ->assertSee(__('home.announcements_title'));
});

it('caps the slider at 7 items', function () {
    User::factory()->count(10)->withProfile(['avatar_path' => 'avatars/test'])->create();

    Livewire::test('parts.home.slider', ['type' => 'profiles'])
        ->assertSet('items', fn ($items) => $items->count() === 7);
});

it('excludes profiles without an avatar or a bio from the profiles slider', function () {
    User::factory()->withProfile(['avatar_path' => null])->create();
    User::factory()->withProfile(['bio' => null, 'avatar_path' => 'avatars/test'])->create();
    User::factory()->withProfile(['avatar_path' => 'avatars/test'])->create();

    Livewire::test('parts.home.slider', ['type' => 'profiles'])
        ->assertSet('items', fn ($items) => $items->count() === 1);
});

it('only loads followed profile ids for the profiles slider', function () {
    $owner = User::factory()->withProfile()->create();
    Announcement::factory()->for($owner)->create();

    Livewire::test('parts.home.slider', ['type' => 'announcements'])
        ->assertSet('followedProfileIds', []);
});

it('shows both sliders on the home page', function () {
    $owner = User::factory()->withProfile(['avatar_path' => 'avatars/test'])->create();
    Announcement::factory()->for($owner)->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(__('home.profiles_title'))
        ->assertSee(__('home.announcements_title'));
});
