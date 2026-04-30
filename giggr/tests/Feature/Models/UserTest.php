<?php

use App\Models\Announcement;
use App\Models\User;

it('can be created with first_name and last_name', function () {
    $user = User::factory()->create(['first_name' => 'Hugo', 'last_name' => 'Girona']);

    expect($user->fresh())
        ->first_name->toBe('Hugo')
        ->last_name->toBe('Girona');
});

it('fullName accessor returns the concatenated name', function () {
    $user = User::factory()->create(['first_name' => 'Hugo', 'last_name' => 'Girona']);

    expect($user->fullName)->toBe('Hugo Girona');
});

it('has many announcements', function () {
    $user = User::factory()->create();
    Announcement::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->announcements)->toHaveCount(3);
});
