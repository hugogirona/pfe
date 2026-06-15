<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }
}
