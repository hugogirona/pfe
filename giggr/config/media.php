<?php

return [
    'disk' => 'public',
    'base_dir' => 'media',
    'variants' => [
        'thumbnail' => ['max_edge' => 400],
        'medium' => ['max_edge' => 1600],
    ],
    'quality' => 85,
    'max_file_size' => 5 * 1024,
    'max_per_profile' => 20,
];
