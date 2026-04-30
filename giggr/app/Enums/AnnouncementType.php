<?php

namespace App\Enums;

enum AnnouncementType: string
{
    case Search    = 'search';
    case Formation = 'formation';
    case Session   = 'session';
    case Course    = 'course';
    case Event     = 'event';

    public function label(): string
    {
        return 'enums.announcement_type.' . $this->value;
    }
}
