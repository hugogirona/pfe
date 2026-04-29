<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Cherche bassiste pour trio jazz — Giggr.')] class extends Component
{
    public array $announcement = [];
    public array $related      = [];

    public function mount(int $id): void
    {
        $this->announcement = [
            'id'               => 1,
            'title'            => 'Cherche bassiste pour trio jazz',
            'type'             => 'Recherche',
            'city'             => 'Liège',
            'instruments'      => ['Basse'],
            'genres'           => ['Jazz', 'Funk'],
            'date'             => '20/04/2026',
            'description'      => "Notre trio jazz (piano, batterie) cherche un bassiste pour compléter la formation. On répète le jeudi soir dans un local de Liège et on joue occasionnellement dans des bars et festivals locaux.

Nous cherchons quelqu'un de motivé, à l'aise sur les standards jazz et ouvert à explorer des sonorités funk et soul. Une expérience scénique est un plus mais pas obligatoire.

Si tu es passionné de groove et que l'idée de construire quelque chose sur la durée t'intéresse, n'hésite pas à nous contacter !",
            'author' => [
                'id'          => 6,
                'name'        => 'Antoine Weber',
                'age'         => 34,
                'city'        => 'Liège',
                'image'       => 'antoine.webp',
                'instruments' => ['Clavier', 'Piano'],
                'genres'      => ['Jazz', 'Electronic'],
            ],
        ];

        // Future: fetch from DB. For now: hardcoded pool scored by relevance.
        $pool = [
            ['id' => 2, 'title' => 'Formation groupe rock alternative',        'type' => 'Formation',  'city' => 'Bruxelles', 'instruments' => ['Guitare', 'Chant'], 'genres' => ['Rock', 'Alternative'], 'date' => '18/04/2026', 'description' => 'Batteur cherche musiciens motivés pour monter un groupe rock/alternative. Influences : Radiohead, Pixies, Interpol.'],
            ['id' => 3, 'title' => 'Musiciens pour mariage en juin',           'type' => 'Événement',  'city' => 'Namur',     'instruments' => ['Violon', 'Guitare'], 'genres' => ['Classique', 'Folk'],  'date' => '15/04/2026', 'description' => 'Cérémonie civile et cocktail le 14 juin. Recherche 2-3 musiciens pour ambiance acoustique élégante.'],
            ['id' => 4, 'title' => 'Jam session hebdomadaire ouverte',         'type' => 'Session',    'city' => 'Charleroi', 'instruments' => [],                   'genres' => ['Jazz', 'Blues', 'Funk'], 'date' => '22/04/2026', 'description' => 'Jam session ouverte tous les mercredis soir. Tous niveaux. Ambiance jazz/blues/funk.'],
            ['id' => 5, 'title' => 'Groupe pop cherche chanteur/chanteuse',    'type' => 'Recherche',  'city' => 'Liège',     'instruments' => ['Chant'],            'genres' => ['Pop', 'Indie'],        'date' => '21/04/2026', 'description' => 'Groupe pop/indie de 4 musiciens cherche voix principale. Répétitions bi-hebdomadaires.'],
            ['id' => 6, 'title' => 'Cours de guitare acoustique proposés',     'type' => 'Cours',      'city' => 'Gand',      'instruments' => ['Guitare'],          'genres' => ['Folk', 'Pop'],         'date' => '10/04/2026', 'description' => 'Guitariste avec 12 ans d\'expérience propose des cours particuliers de guitare acoustique.'],
            ['id' => 7, 'title' => 'Session d\'enregistrement — collaboration','type' => 'Session',    'city' => 'Mons',      'instruments' => ['Saxophone', 'Trompette'], 'genres' => ['Jazz'],          'date' => '23/04/2026', 'description' => 'Studio home disponible pour session jazz. Cherche cuivres pour compléter une maquette.'],
            ['id' => 8, 'title' => 'Groupe metal cherche guitariste lead',     'type' => 'Recherche',  'city' => 'Anvers',    'instruments' => ['Guitare'],          'genres' => ['Metal'],               'date' => '19/04/2026', 'description' => 'Groupe metal actif cherche guitariste lead. Bonne maîtrise technique requise.'],
        ];

        $this->related = $this->computeRelated($this->announcement, $pool);
    }

    private function computeRelated(array $current, array $pool): array
    {
        return collect($pool)
            ->filter(fn($a) => $a['id'] !== $current['id'])
            ->map(function ($a) use ($current) {
                $score  = count(array_intersect($a['instruments'], $current['instruments'])) * 3;
                $score += count(array_intersect($a['genres'],      $current['genres']))      * 2;
                $score += $a['city'] === $current['city'] ? 1 : 0;
                return array_merge($a, ['_score' => $score]);
            })
            ->sortByDesc('_score')
            ->take(3)
            ->values()
            ->toArray();
    }
};
?>

<div>

    <x-parts.announcement.hero :announcement="$announcement" />

    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">

        {{-- 2-col layout --}}
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Main content --}}
            <div class="flex-1 min-w-0 space-y-6 order-2 lg:order-1">
                <x-parts.announcement.tags :announcement="$announcement" />
                <x-parts.announcement.description :announcement="$announcement" />
            </div>

            {{-- Sidebar --}}
            <aside class="w-full lg:w-72 shrink-0 lg:sticky lg:top-24 order-1 lg:order-2">
                <x-parts.announcement.author-card :author="$announcement['author']" />
            </aside>

        </div>

        {{-- Related announcements --}}
        <x-parts.announcement.related :suggestions="$related" />

    </div>

</div>
