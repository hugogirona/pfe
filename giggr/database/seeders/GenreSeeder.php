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
            'Hip-hop', 'Rap', 'R&B', 'Reggae', 'Dancehall', 'Shatta',
            'Électro', 'House', 'Techno', 'Disco', 'Punk', 'Hardcore',
            'Grunge', 'Country', 'Gospel', 'Ska', 'Reggaeton', 'Afrobeat',
            'Trap', 'Latin', 'Salsa', 'Bossa nova', 'Variété',
        ];

        foreach ($genres as $name) {
            Genre::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
