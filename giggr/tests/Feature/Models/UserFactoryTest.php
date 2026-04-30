<?php

use App\Models\Profile;
use App\Models\User;

it('withProfile() state creates a profile for the user', function () {
    $user = User::factory()->withProfile()->create();

    expect(Profile::where('user_id', $user->id)->exists())->toBeTrue();
});
