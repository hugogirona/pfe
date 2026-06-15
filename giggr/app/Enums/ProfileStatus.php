<?php

namespace App\Enums;

enum ProfileStatus: string
{
    case Newcomer = 'newcomer';
    case LookingForBand = 'looking_for_band';
    case AvailableForSessions = 'available_for_sessions';
    case Teaching = 'teaching';
    case OpenToCollab = 'open_to_collab';
    case NotAvailable = 'not_available';

    public function label(): string
    {
        return 'enums.profile_status.'.$this->value;
    }

    /**
     * Statuses a member can pick themselves. Newcomer is the automatic state
     * assigned at account creation and replaced once a real status is chosen,
     * so it is never offered in the selector.
     *
     * @return list<self>
     */
    public static function selectable(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $status): bool => $status !== self::Newcomer,
        ));
    }
}
