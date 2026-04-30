<?php

use App\Models\Announcement;
use App\Models\User;

it('has many announcements', function () {
    $user = User::factory()->create();
    Announcement::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->announcements)->toHaveCount(3);
});
