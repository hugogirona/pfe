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
        :title="__('home.text_image_1_title')"
        :content="__('home.text_image_1_content')"
        :button-label="__('home.text_image_1_cta')"
        url="#"
        image="home2.webp"
        alt="Instruments de musique et matériel audio"
        orientation="right"
        bg="bg-pastel-salmon"
    />

    <x-parts.home.features />

    <x-text-image
        :title="__('home.text_image_2_title')"
        :content="__('home.text_image_2_content')"
        :button-label="__('home.text_image_2_cta')"
        url="#"
        image="home1.webp"
        alt="Un chanteur sur scène"
        orientation="left"
        bg="bg-pastel-blue"
    />
    
    <x-parts.home.musician-slider />

</div>
