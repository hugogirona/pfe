<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function contact(User $viewer, User $recipient): bool
    {
        return $viewer->id !== $recipient->id
            && $recipient->canBeContactedBy($viewer);
    }

    public function block(User $user, User $target): bool
    {
        return $user->id !== $target->id;
    }
}
