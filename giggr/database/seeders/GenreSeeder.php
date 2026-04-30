<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Rock', 'Jazz', 'Pop', 'Folk', 'Metal', 'Classique',
            'Electronic', 'Soul', 'Indie', 'Blues', 'World', 'Funk',
        ];

        foreach ($genres as $name) {
            Genre::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
