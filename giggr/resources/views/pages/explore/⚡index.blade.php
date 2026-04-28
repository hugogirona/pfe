<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Explorer — Giggr.')] class extends Component
{
    public array $musicians = [
        ['id' => 1,  'name' => 'Valentine Martin',  'age' => 23, 'city' => 'Liège',      'bio' => 'Guitariste rock depuis 8 ans, je cherche un groupe pour jouer des originaux et partir en tournée.',       'instruments' => ['Guitare'],    'genres' => ['Rock', 'Blues'],      'image' => 'valentine.webp'],
        ['id' => 2,  'name' => 'Thomas Dubois',      'age' => 31, 'city' => 'Bruxelles',  'bio' => 'Batteur jazz & funk passionné, expérimenté en studio. Disponible pour projets réguliers ou sessions.',    'instruments' => ['Batterie'],   'genres' => ['Jazz', 'Funk'],       'image' => 'thomas.webp'],
        ['id' => 3,  'name' => 'Sarah Chen',          'age' => 28, 'city' => 'Namur',      'bio' => 'Violoniste classique ouverte au folk et aux collaborations interdisciplinaires. 15 ans de pratique.',      'instruments' => ['Violon'],     'genres' => ['Classique', 'Folk'],  'image' => 'sarah.webp'],
        ['id' => 4,  'name' => 'Maxime Leroy',        'age' => 19, 'city' => 'Charleroi',  'bio' => 'Bassiste metal et punk avec une énergie scénique débordante. Je cherche un groupe pour jouer live.',       'instruments' => ['Basse'],      'genres' => ['Metal', 'Punk'],      'image' => 'maxime.webp'],
        ['id' => 5,  'name' => 'Lucie Bernard',       'age' => 25, 'city' => 'Gand',       'bio' => 'Chanteuse pop et soul. Voix travaillée, à l\'aise sur scène. Cherche collaboration sérieuse.',            'instruments' => ['Chant'],      'genres' => ['Pop', 'Soul'],        'image' => 'lucie.webp'],
        ['id' => 6,  'name' => 'Antoine Weber',       'age' => 34, 'city' => 'Liège',      'bio' => 'Pianiste et claviériste jazz et électronique. Compositeur à mes heures, j\'adore fusionner les genres.',  'instruments' => ['Clavier'],    'genres' => ['Jazz', 'Electronic'], 'image' => 'antoine.webp'],
        ['id' => 7,  'name' => 'Inès Fontaine',       'age' => 22, 'city' => 'Bruxelles',  'bio' => 'Guitariste acoustique folk/indie. J\'écris mes propres chansons et cherche une scène pour les partager.',  'instruments' => ['Guitare'],    'genres' => ['Folk', 'Indie'],      'image' => 'ines.webp'],
        ['id' => 8,  'name' => 'Raphaël Morin',       'age' => 27, 'city' => 'Mons',       'bio' => 'Saxophoniste jazz et funk, habitué des jam sessions. Open à tout projet musical ambitieux.',              'instruments' => ['Saxophone'],  'genres' => ['Jazz', 'Funk'],       'image' => ''],
        ['id' => 9,  'name' => 'Camille Petit',       'age' => 29, 'city' => 'Liège',      'bio' => 'Percussionniste world et afro, formée au Conservatoire. Passion pour les rythmes de l\'Afrique de l\'Ouest.', 'instruments' => ['Percussions'], 'genres' => ['World', 'Afro'],    'image' => ''],
        ['id' => 10, 'name' => 'Nicolas Baert',       'age' => 24, 'city' => 'Anvers',     'bio' => 'Bassiste rock et alternative. Solide groove, lecteur de partitions. Cherche groupe avec ambition.',       'instruments' => ['Basse'],      'genres' => ['Rock', 'Alternative'], 'image' => ''],
        ['id' => 11, 'name' => 'Emma Rousseau',       'age' => 26, 'city' => 'Namur',      'bio' => 'Chanteuse et guitariste indie/alternative, auteure-compositrice. Cherche collaborateurs pour album.',     'instruments' => ['Guitare', 'Chant'], 'genres' => ['Indie', 'Alternative'], 'image' => ''],
        ['id' => 12, 'name' => 'Julien Lambert',      'age' => 33, 'city' => 'Bruxelles',  'bio' => 'Trompettiste jazz et classique, professeur en académie. Disponible pour projets le week-end.',           'instruments' => ['Trompette'],  'genres' => ['Jazz', 'Classique'],  'image' => ''],
    ];

    public array $announcements = [
        ['id' => 1, 'title' => 'Cherche bassiste pour trio jazz',          'type' => 'Recherche',  'city' => 'Liège',     'instruments' => ['Basse'],      'genres' => ['Jazz'],             'date' => '20/04/2026', 'description' => 'Notre trio jazz (piano, batterie) cherche un bassiste pour compléter la formation. On répète le jeudi soir et joue occasionnellement dans des bars liégeois.'],
        ['id' => 2, 'title' => 'Formation groupe rock alternative',        'type' => 'Formation',  'city' => 'Bruxelles', 'instruments' => ['Guitare', 'Chant'], 'genres' => ['Rock', 'Alternative'], 'date' => '18/04/2026', 'description' => 'Je suis batteur et cherche des musiciens motivés pour monter un groupe rock/alternative. Influences : Radiohead, Pixies, Interpol. Objectif : enregistrement et concerts.'],
        ['id' => 3, 'title' => 'Musiciens pour mariage en juin',          'type' => 'Événement',  'city' => 'Namur',     'instruments' => ['Violon', 'Guitare'], 'genres' => ['Classique', 'Folk'], 'date' => '15/04/2026', 'description' => 'Cérémonie civile et cocktail le 14 juin. Recherche 2-3 musiciens pour ambiance acoustique élégante. Rémunération prévue.'],
        ['id' => 4, 'title' => 'Jam session hebdomadaire ouverte',        'type' => 'Session',    'city' => 'Charleroi', 'instruments' => [],             'genres' => ['Jazz', 'Blues', 'Funk'], 'date' => '22/04/2026', 'description' => 'Jam session ouverte tous les mercredis soir dans un bar du centre. Tous niveaux acceptés, tous instruments. Ambiance jazz/blues/funk, parfois on déborde vers le rock.'],
        ['id' => 5, 'title' => 'Groupe pop cherche chanteur/chanteuse',   'type' => 'Recherche',  'city' => 'Liège',     'instruments' => ['Chant'],      'genres' => ['Pop', 'Indie'],     'date' => '21/04/2026', 'description' => 'Groupe pop/indie de 4 musiciens (guitare, basse, clavier, batterie) cherche voix principale. On joue des covers et composons nos propres titres. Répétitions bi-hebdomadaires.'],
        ['id' => 6, 'title' => 'Cours de guitare acoustique proposés',    'type' => 'Cours',      'city' => 'Gand',      'instruments' => ['Guitare'],    'genres' => ['Folk', 'Pop'],      'date' => '10/04/2026', 'description' => 'Guitariste avec 12 ans d\'expérience propose des cours particuliers de guitare acoustique. Débutants et intermédiaires bienvenus. Finger-picking, accords, théorie musicale.'],
        ['id' => 7, 'title' => 'Session d\'enregistrement — collaboration', 'type' => 'Session',  'city' => 'Mons',      'instruments' => ['Saxophone', 'Trompette'], 'genres' => ['Jazz'],  'date' => '23/04/2026', 'description' => 'Studio home équipé disponible pour session d\'enregistrement jazz. Je cherche des cuivres (saxo, trompette) pour compléter ma maquette. Projet non-commercial.'],
        ['id' => 8, 'title' => 'Groupe metal cherche guitariste lead',    'type' => 'Recherche',  'city' => 'Anvers',    'instruments' => ['Guitare'],    'genres' => ['Metal'],            'date' => '19/04/2026', 'description' => 'Groupe metal actif depuis 3 ans (EP enregistré, concerts réguliers) cherche guitariste lead pour remplacer un membre partant. Bonne maîtrise technique requise.'],
    ];

    public array $instruments = ['Guitare', 'Basse', 'Batterie', 'Clavier', 'Violon', 'Chant', 'Saxophone', 'Trompette', 'Percussions'];
    public array $genres      = ['Rock', 'Jazz', 'Pop', 'Folk', 'Metal', 'Classique', 'Electronic', 'Soul', 'Indie', 'Blues', 'World', 'Funk'];
};
?>

<div x-data="explorerPage(@js($musicians), @js($announcements))">

    <x-page-header :title="__('explore.title')" :subtitle="__('explore.subtitle')" />

    <x-parts.explore.filter-drawer :instruments="$instruments" :genres="$genres" />

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <x-parts.explore.tab-switcher />

        <div
            x-show="activeTab === 'musiciens'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($musicians as $musician)
                    <div x-show="filteredMusicians.some(m => m.id === {{ $musician['id'] }})">
                        <x-musician-card :musician="$musician" />
                    </div>
                @endforeach
            </div>
            <template x-if="filteredMusicians.length === 0">
                <x-parts.explore.empty-state />
            </template>
        </div>

        <div
            x-show="activeTab === 'annonces'"
            style="display:none"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($announcements as $announcement)
                    <div x-show="filteredAnnouncements.some(a => a.id === {{ $announcement['id'] }})">
                        <x-parts.explore.announcement-card :announcement="$announcement" />
                    </div>
                @endforeach
            </div>
            <template x-if="filteredAnnouncements.length === 0">
                <x-parts.explore.empty-state />
            </template>
        </div>

    </div>

</div>
