<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return $this->view()->title(__('titles.privacy'));
    }
};
?>

<div>

    <x-page-header
        :title="__('privacy.title')"
        :subtitle="__('privacy.subtitle')"
    />

    <div class="max-w-3xl mx-auto px-6 py-16 md:py-24">

        <p class="text-sm text-caption mb-12">
            {{ __('privacy.last_updated', ['date' => now()->translatedFormat('d F Y')]) }}
        </p>

        <div class="space-y-12">

            <x-legal.section :heading="__('privacy.intro_heading')">
                <p>{{ __('privacy.intro_body') }}</p>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.data_heading')">
                <p>{{ __('privacy.data_body') }}</p>
                <ul class="list-disc pl-5 space-y-1.5 marker:text-accent">
                    @foreach (__('privacy.data_items') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.purpose_heading')">
                <p>{{ __('privacy.purpose_body') }}</p>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.retention_heading')">
                <p>{{ __('privacy.retention_body') }}</p>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.sharing_heading')">
                <p>{{ __('privacy.sharing_body') }}</p>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.rights_heading')">
                <p>{{ __('privacy.rights_body') }}</p>
                <ul class="list-disc pl-5 space-y-1.5 marker:text-accent">
                    @foreach (__('privacy.rights_items') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </x-legal.section>

            <x-legal.section :heading="__('privacy.contact_heading')">
                <p>
                    {!! __('privacy.contact_body', [
                        'link' => '<a href="'.route('contact').'" wire:navigate
                                      class="text-accent underline underline-offset-2 hover:opacity-80 transition-opacity duration-150
                                      focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">'
                                . __('privacy.contact_cta')
                                . '</a>',
                    ]) !!}
                </p>
            </x-legal.section>

        </div>
    </div>

</div>
