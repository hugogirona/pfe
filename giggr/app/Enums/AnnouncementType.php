<?php

namespace App\Enums;

enum AnnouncementType: string
{
    case Search = 'search';
    case Formation = 'formation';
    case Session = 'session';
    case Course = 'course';
    case Event = 'event';

    public function label(): string
    {
        return 'enums.announcement_type.'.$this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::Search => 'bg-accent text-bg',
            self::Formation => 'bg-dark text-bg',
            self::Session => 'bg-pastel-blue text-dark',
            self::Course => 'bg-pastel-salmon text-dark',
            self::Event => 'bg-pastel-taupe text-dark',
        };
    }
}
