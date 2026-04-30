<?php

namespace App\Enums;

enum AnnouncementStatus: string
{
    case Open    = 'open';
    case Closed  = 'closed';
    case Expired = 'expired';

    public function label(): string
    {
        return 'enums.announcement_status.' . $this->value;
    }
}
