<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
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
        :url="route('explore', ['tab' => __('explore.tab_announcements_slug')])"
        image="home2.webp"
        alt="Instruments de musique et matériel audio"
        orientation="right"
        bg="bg-pastel-salmon"
    />

    <livewire:parts.home.slider type="announcements" bg="bg-bg" />

    <x-parts.home.features />

    <x-text-image
        :title="__('home.text_image_2_title')"
        :content="__('home.text_image_2_content')"
        :button-label="__('home.text_image_2_cta')"
        :url="route('explore', ['tab' => __('explore.tab_profiles_slug')])"
        image="home1.webp"
        alt="Un chanteur sur scène"
        orientation="left"
        bg="bg-pastel-blue"
    />

    <livewire:parts.home.slider type="profiles" bg="bg-bg" />

</div>
