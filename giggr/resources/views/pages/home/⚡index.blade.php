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

    <x-text-image
        title="Comment ça marche ?"
        content="Connecte-toi avec des musiciens amateurs de ta région. Forme ton groupe, organise des jam sessions et partage ta passion pour la musique."
        button-label="S'inscrire"
        url="#"
        image="home1.webp"
        alt="Un chanteur sur scène"
        orientation="left"
        bg="bg-pastel-blue"
    />

    <x-text-image
        title="Un titre accrocheur"
        content="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
        button-label="Button cta"
        url="#"
        image="home2.webp"
        alt="Instruments de musique et matériel audio"
        orientation="right"
        bg="bg-pastel-salmon"
    />
</div>
