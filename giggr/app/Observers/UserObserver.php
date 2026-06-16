<?php

namespace App\Observers;

use App\Models\Follow;
use App\Models\User;

class UserObserver
{
    /**
     * Remove follows pointing at the user's profile before the account is
     * deleted. The follows table only cascades on the follower side (user_id),
     * so inbound follows would otherwise become orphans and inflate the
     * followed_count shown on the identity card.
     */
    public function deleting(User $user): void
    {
        if ($user->profile === null) {
            return;
        }

        Follow::query()
            ->where('followable_type', $user->profile->getMorphClass())
            ->where('followable_id', $user->profile->id)
            ->delete();
    }
}
