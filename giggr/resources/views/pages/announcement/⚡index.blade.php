<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Cherche bassiste pour trio jazz — Giggr.')] class extends Component
{
    public array $announcement = [];

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
            'level'            => 'Intermédiaire',
            'rehearsal_rhythm' => '1× par semaine (jeudi soir)',
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
    }
};
?>

<div>

    <x-parts.announcement.hero :announcement="$announcement" />

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Main content --}}
            <div class="flex-1 min-w-0 space-y-6 order-2 lg:order-1">
                <x-parts.announcement.tags :announcement="$announcement" />
                <x-parts.announcement.description :announcement="$announcement" />
                <x-parts.announcement.practical-info :announcement="$announcement" />
            </div>

            {{-- Sidebar --}}
            <aside class="w-full lg:w-72 shrink-0 lg:sticky lg:top-24 order-1 lg:order-2">
                <x-parts.announcement.author-card :author="$announcement['author']" />
            </aside>

        </div>
    </div>

</div>
