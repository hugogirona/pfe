<?php

use App\Actions\NotifyFollowersOfNewAnnouncement;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Support\Facades\Notification;

it('notifies every follower of the author', function () {
    Notification::fake();

    $author = User::factory()->withProfile()->create();
    $followerA = User::factory()->withProfile()->create();
    $followerB = User::factory()->withProfile()->create();
    $followerA->follow($author->profile);
    $followerB->follow($author->profile);

    $announcement = Announcement::factory()->for($author)->create();

    app(NotifyFollowersOfNewAnnouncement::class)->execute($announcement);

    Notification::assertSentTo([$followerA, $followerB], NewAnnouncement::class);
});

it('does not notify the author themselves', function () {
    Notification::fake();

    $author = User::factory()->withProfile()->create();
    $announcement = Announcement::factory()->for($author)->create();

    app(NotifyFollowersOfNewAnnouncement::class)->execute($announcement);

    Notification::assertNotSentTo($author, NewAnnouncement::class);
});

it('does not notify users who do not follow the author', function () {
    Notification::fake();

    $author = User::factory()->withProfile()->create();
    $stranger = User::factory()->withProfile()->create();
    $announcement = Announcement::factory()->for($author)->create();

    app(NotifyFollowersOfNewAnnouncement::class)->execute($announcement);

    Notification::assertNotSentTo($stranger, NewAnnouncement::class);
});
