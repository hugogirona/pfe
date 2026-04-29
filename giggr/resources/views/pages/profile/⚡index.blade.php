<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Valentine Martin — Giggr.')] class extends Component
{
    public array $musician = [
        'id'           => 1,
        'name'         => 'Valentine Martin',
        'age'          => 23,
        'city'         => 'Liège',
        'experience'   => 8,
        'active_ads'   => 2,
        'status'       => 'Cherche groupe',
        'image'        => 'valentine.webp',
        'bio'          => "Guitariste rock depuis 8 ans, je cherche un groupe pour jouer des originaux et partir en tournée. Passionnée par le son vintage et les effets de pédale, j'ai joué dans plusieurs groupes amateurs et participé à des open mics liégeois. J'écoute beaucoup de Led Zeppelin, SRV et des trucs plus modernes comme Palaye Royale. Mon objectif : enregistrer un EP et fouler une vraie scène.",
        'instruments'  => ['Guitare électrique', 'Guitare acoustique'],
        'genres'       => ['Rock', 'Blues', 'Alternative'],
        'media'        => [
            [
                'type'        => 'photo',
                'src'         => '',
                'description' => 'Live au festival Rock de Liège, juin 2025.',
            ],
            [
                'type'        => 'video',
                'src'         => '',
                'thumbnail'   => '',
                'description' => "Cover de « Since I've Been Loving You » — Led Zeppelin.",
            ],
            [
                'type'        => 'photo',
                'src'         => '',
                'description' => "Session d'enregistrement en studio, mars 2025.",
            ],
            [
                'type'        => 'photo',
                'src'         => '',
                'description' => 'Répétition avec Les Voltés, automne 2024.',
            ],
        ],
        'announcements' => [
            [
                'id'          => 1,
                'title'       => 'Guitariste cherche groupe rock',
                'type'        => 'Recherche',
                'city'        => 'Liège',
                'instruments' => ['Batterie', 'Basse'],
                'genres'      => ['Rock', 'Blues'],
                'date'        => '20/04/2026',
                'description' => "Je cherche un groupe soudé pour jouer des originaux. Influences : Led Zeppelin, Hendrix, SRV.",
            ],
            [
                'id'          => 5,
                'title'       => 'Jam session rock le samedi matin',
                'type'        => 'Session',
                'city'        => 'Liège',
                'instruments' => [],
                'genres'      => ['Rock', 'Blues'],
                'date'        => '22/04/2026',
                'description' => "Jam ouverte tous les samedis dans mon local. Tous niveaux, ambiance détendue.",
            ],
        ],
    ];
};
?>

<div>

    <x-parts.profile.hero :musician="$musician" />

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Sidebar --}}
            <aside class="w-full lg:w-80 shrink-0 lg:sticky lg:top-24" aria-label="{{ $musician['name'] }}">
                <x-parts.profile.identity-card :musician="$musician" />
            </aside>

            {{-- Main content --}}
            <div class="flex-1 min-w-0 space-y-6">
                <x-parts.profile.about :musician="$musician" />
                <x-parts.profile.media-gallery :musician="$musician" />
                <x-parts.profile.announcements :musician="$musician" />
            </div>

        </div>
    </div>

</div>
