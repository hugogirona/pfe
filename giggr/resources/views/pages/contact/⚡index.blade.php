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
        <div class="grid grid-cols-1 lg:grid-cols-5 items-start gap-y-10 gap-x-10 xl:gap-x-16">

            <div class="lg:col-span-2">
                <x-parts.contact.info />
            </div>

            <div class="lg:col-span-3">
                <x-parts.contact.form />
            </div>

        </div>
    </div>

</div>
