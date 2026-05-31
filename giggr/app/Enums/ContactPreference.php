<?php

namespace App\Enums;

enum ContactPreference: string
{
    case Everyone = 'everyone';
    case FollowersOnly = 'followers_only';
    case Nobody = 'nobody';

    public function label(): string
    {
        return 'enums.contact_preference.'.$this->value;
    }
}
