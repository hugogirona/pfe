<?php

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;

it('is delivered through the database channel', function () {
    $announcement = Announcement::factory()->create();

    expect((new NewAnnouncement($announcement))->via(User::factory()->make()))->toBe(['database']);
});

it('stores the author identity and the announcement link in its payload', function () {
    $author = User::factory()->withProfile()->create();
    $announcement = Announcement::factory()->for($author)->create(['title' => 'Cherche batteur']);

    $data = (new NewAnnouncement($announcement))->toArray(User::factory()->make());

    expect($data)
        ->toHaveKeys(['actor_user_id', 'actor_profile_id', 'actor_name', 'announcement_id', 'announcement_title'])
        ->and($data['actor_user_id'])->toBe($author->id)
        ->and($data['actor_profile_id'])->toBe($author->profile->id)
        ->and($data['actor_name'])->toBe($author->full_name)
        ->and($data['announcement_id'])->toBe($announcement->id)
        ->and($data['announcement_title'])->toBe('Cherche batteur');
});
