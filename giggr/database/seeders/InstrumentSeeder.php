<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstrumentSeeder extends Seeder
{
    public function run(): void
    {
        $instruments = [
            'Guitare', 'Basse', 'Batterie', 'Clavier', 'Violon',
            'Chant', 'Saxophone', 'Trompette', 'Percussions',
            'Piano', 'Synthétiseur', 'Orgue', 'Platines DJ', 'Ukulélé',
            'Violoncelle', 'Contrebasse', 'Flûte', 'Clarinette', 'Trombone',
            'Harpe', 'Accordéon', 'Banjo', 'Mandoline', 'Harmonica',
            'Triangle', 'Tambourin', 'Djembé', 'Congas', 'Cajón',
        ];

        foreach ($instruments as $name) {
            Instrument::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
