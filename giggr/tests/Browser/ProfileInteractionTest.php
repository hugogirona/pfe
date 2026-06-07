<?php

use App\Models\User;

it('permet à un membre connecté de suivre un autre musicien', function () {
    $viewer = User::factory()->create();
    $target = User::factory()->withProfile()->create();

    $this->actingAs($viewer);

    $page = visit(path('profile', ['id' => $target->profile->id]));

    $page->assertSee(__('social.follow'))
        ->click(__('social.follow'))
        ->assertSee(__('social.following'));
});
