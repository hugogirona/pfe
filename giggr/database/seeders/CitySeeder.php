<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Liège',      'slug' => 'liege',      'latitude' => 50.6326, 'longitude' => 5.5797],
            ['name' => 'Bruxelles',  'slug' => 'bruxelles',  'latitude' => 50.8503, 'longitude' => 4.3517],
            ['name' => 'Namur',      'slug' => 'namur',      'latitude' => 50.4674, 'longitude' => 4.8720],
            ['name' => 'Charleroi',  'slug' => 'charleroi',  'latitude' => 50.4108, 'longitude' => 4.4446],
            ['name' => 'Gand',       'slug' => 'gand',       'latitude' => 51.0543, 'longitude' => 3.7174],
            ['name' => 'Mons',       'slug' => 'mons',       'latitude' => 50.4542, 'longitude' => 3.9523],
            ['name' => 'Anvers',     'slug' => 'anvers',     'latitude' => 51.2194, 'longitude' => 4.4025],
            ['name' => 'Louvain',    'slug' => 'louvain',    'latitude' => 50.8798, 'longitude' => 4.7005],
            ['name' => 'Bruges',     'slug' => 'bruges',     'latitude' => 51.2093, 'longitude' => 3.2247],
            ['name' => 'Tournai',    'slug' => 'tournai',    'latitude' => 50.6056, 'longitude' => 3.3886],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['slug' => $city['slug']],
                [
                    'name'      => $city['name'],
                    'country'   => 'BE',
                    'latitude'  => $city['latitude'],
                    'longitude' => $city['longitude'],
                ],
            );
        }
    }
}
