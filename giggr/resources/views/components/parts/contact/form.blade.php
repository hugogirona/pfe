<section>
    <h2 id="contact-form-heading"
        class="font-heading text-3xl md:text-4xl text-heading mb-8">
        {{ __('contact.form_title') }}
    </h2>

    @if (session('contact_success'))
        <div
            role="status"
            aria-live="polite"
            class="mb-6 rounded-[6px] bg-pastel-salmon/40 border border-accent/30 px-4 py-3"
        >
            <p class="font-medium text-body">{{ __('contact.form_success_title') }}</p>
            <p class="text-sm text-subtle mt-0.5">{{ __('contact.form_success_body') }}</p>
        </div>
    @endif

    <form
        action="{{ route('contact.submit') }}"
        method="POST"
        novalidate
        aria-labelledby="contact-form-heading"
        class="space-y-5"
    >
        @csrf
        <x-honeypot/>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-form.input
                name="first_name"
                type="text"
                :label="__('contact.form_first_name')"
                :required="true"
                autocomplete="given-name"
            />

            <x-form.input
                name="last_name"
                type="text"
                :label="__('contact.form_last_name')"
                :required="true"
                autocomplete="family-name"
            />
        </div>

        <x-form.input
            name="email"
            type="email"
            :label="__('contact.form_email')"
            :required="true"
            autocomplete="email"
        />

        <x-form.select
            name="subject"
            :label="__('contact.form_subject')"
            :required="true"
        >
            <option value="" disabled @selected(old('subject') === null)>{{ __('contact.subject_placeholder') }}</option>
            <option value="general" @selected(old('subject') === 'general')>{{ __('contact.subject_general') }}</option>
            <option value="partnership" @selected(old('subject') === 'partnership')>{{ __('contact.subject_partnership') }}</option>
            <option value="feature" @selected(old('subject') === 'feature')>{{ __('contact.subject_feature') }}</option>
            <option value="bug" @selected(old('subject') === 'bug')>{{ __('contact.subject_bug') }}</option>
            <option value="other" @selected(old('subject') === 'other')>{{ __('contact.subject_other') }}</option>
        </x-form.select>

        <x-form.textarea
            name="message"
            :label="__('contact.form_message')"
            :required="true"
            :rows="5"
        />

        <x-form.checkbox name="rgpd" :required="true" :checked="old('rgpd')" value="1">
            {!! __('contact.rgpd_label', [
                'link' => '<a href="'.route('privacy').'" wire:navigate
                              class="text-accent underline underline-offset-2 hover:opacity-80 transition-opacity duration-150
                              focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">'
                        . __('contact.rgpd_policy')
                        . '</a>',
            ]) !!}
        </x-form.checkbox>

        <p class="text-xs text-caption">{{ __('contact.required_legend') }}</p>

        <x-cta type="submit" size="lg" class="w-full min-h-[44px] gap-2 mt-1">
            {{ __('contact.form_submit') }}
            <x-icon name="arrow-right" class="w-4 h-4" />
        </x-cta>
    </form>
</section>
