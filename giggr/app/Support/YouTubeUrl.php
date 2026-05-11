<?php

namespace App\Support;

class YouTubeUrl
{
    private const string ID_PATTERN = '[A-Za-z0-9_-]{11}';

    public static function extractId(string $input): ?string
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }

        $patterns = [
            '#youtu\.be/('.self::ID_PATTERN.')(?:[?&].*)?$#',
            '#youtube\.com/embed/('.self::ID_PATTERN.')(?:[?&].*)?$#',
            '#youtube\.com/watch\?(?:.*&)?v=('.self::ID_PATTERN.')(?:&.*)?$#',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }
}
