<?php

namespace App\Enums;

enum AnnouncementType: string
{
    case MusicianWanted = 'musician_wanted';
    case BandWanted = 'band_wanted';
    case Gig = 'gig';
    case Lessons = 'lessons';

    public function label(): string
    {
        return 'enums.announcement_type.'.$this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::MusicianWanted => 'bg-accent text-on-dark',
            self::BandWanted => 'bg-dark text-on-dark',
            self::Gig => 'bg-pastel-blue text-body',
            self::Lessons => 'bg-pastel-salmon text-body',
        };
    }
}
