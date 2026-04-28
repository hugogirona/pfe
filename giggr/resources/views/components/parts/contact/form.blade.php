<section>
    <h2 id="contact-form-heading"
        class="font-heading text-3xl md:text-4xl text-dark mb-8">
        {{ __('contact.form_title') }}
    </h2>

    <form
        action="#"
        method="POST"
        novalidate
        aria-labelledby="contact-form-heading"
        class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-form.input
                name="name"
                type="text"
                :label="__('contact.form_name')"
                :required="true"
                autocomplete="name"
            />
            <x-form.input
                name="email"
                type="email"
                :label="__('contact.form_email')"
                :required="true"
                autocomplete="email"
            />
        </div>

        <x-form.select
            name="subject"
            :label="__('contact.form_subject')"
            :required="true">
            <option value="" disabled selected>{{ __('contact.subject_placeholder') }}</option>
            <option value="general">{{ __('contact.subject_general') }}</option>
            <option value="partnership">{{ __('contact.subject_partnership') }}</option>
            <option value="bug">{{ __('contact.subject_bug') }}</option>
            <option value="other">{{ __('contact.subject_other') }}</option>
        </x-form.select>

        <x-form.textarea
            name="message"
            :label="__('contact.form_message')"
            :required="true"
            :rows="5"
        />

        <x-form.checkbox name="rgpd" :required="true">
            {!! __('contact.rgpd_label', [
                'link' => '<a href="#"
                              class="text-accent underline underline-offset-2 hover:opacity-80 transition-opacity duration-150
                                     focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">'
                        . __('contact.rgpd_policy')
                        . '</a>',
            ]) !!}
        </x-form.checkbox>

        <p class="text-xs text-dark/40">{{ __('contact.required_legend') }}</p>

        <button
            type="submit"
            class="w-full min-h-[44px] inline-flex items-center justify-center gap-2
                   px-6 py-3 text-base font-medium rounded-[6px] cursor-pointer
                   bg-dark text-bg hover:opacity-80 transition-opacity duration-150
                   focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/50 focus-visible:ring-offset-2">
            {{ __('contact.form_submit') }}
            <x-icon name="arrow-right" class="w-4 h-4" />
        </button>

    </form>
</section>
