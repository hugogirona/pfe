<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Accueil')] class extends Component
{

};
?>

<div>
    <x-parts.home.hero />

    <x-parts.home.partners />

    <x-text-image
        title="Trouve le musicien qu'il te manque"
        content="Guitariste, batteur, claviériste ou chanteur — parcours des centaines de profils et connecte-toi avec des musiciens amateurs de ta région qui partagent ta passion."
        button-label="Explorer les annonces"
        url="#"
        image="home2.webp"
        alt="Instruments de musique et matériel audio"
        orientation="right"
        bg="bg-pastel-salmon"
    />

    <x-parts.home.features />

    <x-text-image
        title="Ta prochaine aventure musicale commence ici"
        content="Crée ton profil en 2 minutes, décris ton univers musical et laisse la communauté venir à toi. C'est gratuit, c'est local, c'est fait pour toi."
        button-label="Explorer les profils"
        url="#"
        image="home1.webp"
        alt="Un chanteur sur scène"
        orientation="left"
        bg="bg-pastel-blue"
    />


    <x-parts.home.musicians />

</div>
