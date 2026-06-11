<?php

namespace App\Actions;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Support\Facades\Notification;

class NotifyFollowersOfNewAnnouncement
{
    public function execute(Announcement $announcement): void
    {
        $profile = $announcement->user->profile;
        if ($profile === null) {
            return;
        }

        $followerIds = $profile->followers()
            ->where('user_id', '!=', $announcement->user_id)
            ->pluck('user_id');

        if ($followerIds->isEmpty()) {
            return;
        }

        $followers = User::whereKey($followerIds)->get();
        Notification::send($followers, new NewAnnouncement($announcement));
    }
}
