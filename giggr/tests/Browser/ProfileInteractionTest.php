<?php

use App\Models\User;

it('lets a logged-in member follow another musician', function () {
    $viewer = User::factory()->create();
    $target = User::factory()->withProfile()->create();

    $this->actingAs($viewer);

    $page = visit(path('profile', ['id' => $target->profile->id]));

    $page->assertSee(__('social.follow'))
        ->click(__('social.follow'))
        ->assertSee(__('social.following'));
});
