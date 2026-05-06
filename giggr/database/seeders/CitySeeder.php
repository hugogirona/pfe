<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class CitySeeder extends Seeder
{
    /**
     * Bilingual aliases for the major bilingual Belgian localities.
     * Format: native canonical name (as found in the CSV) => alternate-language name.
     * The seeder applies this in both directions.
     */
    private const array ALIASES = [
        // Brussels-Capital
        'Bruxelles' => 'Brussel',
        'Schaerbeek' => 'Schaarbeek',
        'Etterbeek' => 'Etterbeek',
        'Ixelles' => 'Elsene',
        'Saint-Gilles' => 'Sint-Gillis',
        'Anderlecht' => 'Anderlecht',
        'Molenbeek-Saint-Jean' => 'Sint-Jans-Molenbeek',
        'Berchem-Sainte-Agathe' => 'Sint-Agatha-Berchem',
        'Jette' => 'Jette',
        'Woluwe-Saint-Pierre' => 'Sint-Pieters-Woluwe',
        'Auderghem' => 'Oudergem',
        'Watermael-Boitsfort' => 'Watermaal-Bosvoorde',
        'Uccle' => 'Ukkel',
        'Forest' => 'Vorst',
        'Woluwe-Saint-Lambert' => 'Sint-Lambrechts-Woluwe',
        'Saint-Josse-Ten-Noode' => 'Sint-Joost-Ten-Node',
        'Laeken' => 'Laken',

        // Major Flemish cities (NL → FR)
        'Antwerpen' => 'Anvers',
        'Brugge' => 'Bruges',
        'Gent' => 'Gand',
        'Leuven' => 'Louvain',
        'Mechelen' => 'Malines',
        'Kortrijk' => 'Courtrai',
        'Ieper' => 'Ypres',
        'Oostende' => 'Ostende',
        'Roeselare' => 'Roulers',
        'Tienen' => 'Tirlemont',
        'Aalst' => 'Alost',
        'Sint-Truiden' => 'Saint-Trond',
        'Halle' => 'Hal',
        'Vilvoorde' => 'Vilvorde',
        'Veurne' => 'Furnes',
        'Diksmuide' => 'Dixmude',
        'Nieuwpoort' => 'Nieuport',
        'Ronse' => 'Renaix',
        'Sint-Niklaas' => 'Saint-Nicolas',
        'Dendermonde' => 'Termonde',
        'Geraardsbergen' => 'Grammont',
        'Lier' => 'Lierre',
        'Turnhout' => 'Turnhout',
        'Geel' => 'Gheel',
        'Zoutleeuw' => 'Léau',

        // Major Walloon cities (FR → NL)
        'Liège' => 'Luik',
        'Mons' => 'Bergen',
        'Namur' => 'Namen',
        'Tournai' => 'Doornik',
        'Ath' => 'Aat',
        'Soignies' => 'Zinnik',
        'La Louvière' => 'La Louvière',
        'Enghien' => 'Edingen',
        'Nivelles' => 'Nijvel',
        'Wavre' => 'Waver',
        'Braine-le-Comte' => 's-Gravenbrakel',
        'Jodoigne' => 'Geldenaken',
    ];

    public function run(): void
    {
        $path = database_path('data/be_localities.csv');

        if (! is_file($path)) {
            throw new RuntimeException("Belgian localities dataset not found at {$path}");
        }

        $aliases = $this->expandedAliases();
        $handle = fopen($path, 'r');
        $batch = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            [$postal, $name, $lng, $lat] = $row;
            $postal = trim($postal);
            $name = trim($name);
            $alt = $aliases[$name] ?? null;
            $slug = Str::slug($name).'-'.$postal;

            $batch[] = [
                'name' => $name,
                'name_alt' => $alt,
                'slug' => $slug,
                'country' => 'BE',
                'postal_code' => $postal,
                'searchable' => City::makeSearchable($name, $alt, $postal),
                'latitude' => is_numeric($lat) ? (float) $lat : null,
                'longitude' => is_numeric($lng) ? (float) $lng : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 200) {
                $this->flush($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->flush($batch);
        }

        fclose($handle);
    }

    /**
     * @param  array<int, array<string, mixed>>  $batch
     */
    private function flush(array $batch): void
    {
        City::upsert(
            $batch,
            ['slug'],
            ['name', 'name_alt', 'country', 'postal_code', 'searchable', 'latitude', 'longitude', 'updated_at'],
        );
    }

    /**
     * @return array<string, string>
     */
    private function expandedAliases(): array
    {
        $expanded = [];
        foreach (self::ALIASES as $native => $alt) {
            $expanded[$native] = $alt;
            $expanded[$alt] ??= $native;
        }

        return $expanded;
    }
}
