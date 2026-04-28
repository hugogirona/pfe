<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Contact — Giggr.')] class extends Component
{
    //
};
?>

<div>

    <x-page-header
        :title="__('contact.title')"
        :subtitle="__('contact.subtitle')"
    />

    <div class="max-w-6xl mx-auto px-6 py-16 md:py-24">

        <div class="flex flex-col lg:flex-row items-start gap-y-14 gap-x-10 xl:gap-x-16">
            <div class="w-full lg:w-1/2 lg:shrink-0">
                <x-parts.contact.form />
            </div>
            <div class="w-full lg:flex-1">
                <x-parts.contact.faq />
            </div>
        </div>

    </div>

</div>
