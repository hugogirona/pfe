<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Youtube = 'youtube';

    public function label(): string
    {
        return 'enums.media_type.'.$this->value;
    }
}
