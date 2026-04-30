<?php

namespace App\Enums;

enum ProfileStatus: string
{
    case LookingForBand         = 'looking_for_band';
    case AvailableForSessions   = 'available_for_sessions';
    case Teaching               = 'teaching';
    case OpenToCollab           = 'open_to_collab';
    case NotAvailable           = 'not_available';

    public function label(): string
    {
        return 'enums.profile_status.' . $this->value;
    }
}
