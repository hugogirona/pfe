<?php

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('profile page renders a real profile', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->actingAs($profile->user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertOk()
        ->assertSee($profile->user->full_name);
});

it('profile page returns 404 for a missing profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile', ['id' => 99999]))
        ->assertNotFound();
});

it('profile page redirects guests to login', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->get(route('profile', ['id' => $profile->id]))
        ->assertRedirectToRoute('login');
});

it('preserves relation counts when the avatar is refreshed', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $follower = User::factory()->create();
    $follower->follow($profile);
    $other = Profile::factory()->create();
    $owner->follow($other);

    $this->actingAs($owner);

    $component = Livewire::test('pages::profile.index', ['id' => $profile->id]);

    expect($component->get('profile')->followers_count)->toBe(1)
        ->and($component->get('profile')->followed_count)->toBe(1);

    $component->dispatch('avatar-saved');

    expect($component->get('profile')->followers_count)->toBe(1)
        ->and($component->get('profile')->followed_count)->toBe(1);
});

it('refreshes followers and followed counts when follow-state-changed is received', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner);

    $component = Livewire::test('pages::profile.index', ['id' => $profile->id]);

    expect($component->get('profile')->followers_count)->toBe(0)
        ->and($component->get('profile')->followed_count)->toBe(0);

    $follower = User::factory()->create();
    $follower->follow($profile);
    $other = Profile::factory()->create();
    $owner->follow($other);

    $component->dispatch('follow-state-changed');

    expect($component->get('profile')->followers_count)->toBe(1)
        ->and($component->get('profile')->followed_count)->toBe(1);
});

it('profile page renders the followers and following counts', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $follower1 = User::factory()->create();
    $follower2 = User::factory()->create();
    $follower1->follow($profile);
    $follower2->follow($profile);

    $followed1 = Profile::factory()->create();
    $followed2 = Profile::factory()->create();
    $followed3 = Profile::factory()->create();
    $owner->follow($followed1);
    $owner->follow($followed2);
    $owner->follow($followed3);

    $response = $this->actingAs(User::factory()->create())
        ->get(route('profile', ['id' => $profile->id]))
        ->assertOk();

    $response->assertSeeTextInOrder(['2', __('social.tab_followers')], false)
        ->assertSeeTextInOrder(['3', __('social.tab_followed')], false);
});
