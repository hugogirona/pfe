<?php

use App\Enums\ContactPreference;
use App\Models\User;
use Livewire\Livewire;

it('shows the contact button when the owner accepts everyone', function () {
    $owner = User::factory()->withProfile()->create(['email_verified_at' => now()]);
    $viewer = User::factory()->withProfile()->create(['email_verified_at' => now()]);

    Livewire::actingAs($viewer)
        ->test('pages::profile.show', ['id' => $owner->profile->id])
        ->assertSeeHtml('parts.messaging.inbox');
});

it('hides the contact button when the owner accepts nobody', function () {
    $owner = User::factory()->withProfile()->create(['email_verified_at' => now()]);
    $owner->profile->update(['contact_preference' => ContactPreference::Nobody]);
    $viewer = User::factory()->withProfile()->create(['email_verified_at' => now()]);

    Livewire::actingAs($viewer)
        ->test('pages::profile.show', ['id' => $owner->profile->id])
        ->assertDontSeeHtml('parts.messaging.inbox');
});

it('hides the contact button from a non-followed viewer under followers_only', function () {
    $owner = User::factory()->withProfile()->create(['email_verified_at' => now()]);
    $owner->profile->update(['contact_preference' => ContactPreference::FollowersOnly]);
    $viewer = User::factory()->withProfile()->create(['email_verified_at' => now()]);

    Livewire::actingAs($viewer)
        ->test('pages::profile.show', ['id' => $owner->profile->id])
        ->assertDontSeeHtml('parts.messaging.inbox');
});

it('shows the contact button to a viewer the owner follows under followers_only', function () {
    $owner = User::factory()->withProfile()->create(['email_verified_at' => now()]);
    $owner->profile->update(['contact_preference' => ContactPreference::FollowersOnly]);
    $viewer = User::factory()->withProfile()->create(['email_verified_at' => now()]);
    $owner->follow($viewer->profile);

    Livewire::actingAs($viewer)
        ->test('pages::profile.show', ['id' => $owner->profile->id])
        ->assertSeeHtml('parts.messaging.inbox');
});
