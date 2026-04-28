<section aria-labelledby="faq-heading">

    <div class="mb-8">
        <h2 id="faq-heading" class="font-heading text-3xl md:text-4xl text-dark mb-2">
            {{ __('contact.faq_heading') }}
        </h2>
        <p class="text-dark/45 text-sm leading-relaxed">
            {{ __('contact.faq_subtitle') }}
        </p>
    </div>

    <div>
        <x-parts.contact.faq-item number="01" :question="__('contact.faq_q1')" :open="true">
            {{ __('contact.faq_a1') }}
        </x-parts.contact.faq-item>

        <x-parts.contact.faq-item number="02" :question="__('contact.faq_q2')">
            {{ __('contact.faq_a2') }}
        </x-parts.contact.faq-item>

        <x-parts.contact.faq-item number="03" :question="__('contact.faq_q3')">
            {{ __('contact.faq_a3') }}
        </x-parts.contact.faq-item>

        <x-parts.contact.faq-item number="04" :question="__('contact.faq_q4')">
            {{ __('contact.faq_a4') }}
        </x-parts.contact.faq-item>

        <x-parts.contact.faq-item number="05" :question="__('contact.faq_q5')">
            {{ __('contact.faq_a5') }}
        </x-parts.contact.faq-item>
    </div>

</section>
