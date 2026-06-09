<?php

use App\Actions\Fortify\CreateNewUser;
use App\Notifications\WelcomeWithVerificationCode;
use Illuminate\Auth\Events\Verified;
use Illuminate\Cache\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component {
    public string $code = '';

    public ?string $error = null;

    public ?string $info = null;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user === null) {
            $this->redirect(route('login'), navigate: true);

            return;
        }
        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('profile', ['id' => $user->profile->id]), navigate: true);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return $this->view()->title(__('titles.verification.notice'));
    }

    public function verify(RateLimiter $limiter): void
    {
        $user = auth()->user();
        abort_unless($user !== null, 403);
        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('profile', ['id' => $user->profile->id]), navigate: true);

            return;
        }

        $throttleKey = 'verify-email-attempt:'.$user->id;
        if ($limiter->tooManyAttempts($throttleKey, 5)) {
            $this->error = __('auth.verify_email_throttled', [
                'seconds' => $limiter->availableIn($throttleKey),
            ]);

            return;
        }

        $normalized = preg_replace('/\D/', '', $this->code) ?? '';

        if (
            $user->email_verification_code === null
            || $user->email_verification_code_expires_at === null
            || $user->email_verification_code_expires_at->isPast()
            || ! hash_equals($user->email_verification_code, $normalized)
        ) {
            $limiter->hit($throttleKey, 60);
            $this->error = __('auth.verify_email_invalid');

            return;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->forceFill([
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ])->save();

        $limiter->clear($throttleKey);

        $this->redirect(route('profile', ['id' => $user->profile->id]), navigate: true);
    }

    public function resend(RateLimiter $limiter): void
    {
        $user = auth()->user();
        abort_unless($user !== null, 403);
        if ($user->hasVerifiedEmail()) {
            $this->info = __('auth.verify_email_already_verified');

            return;
        }

        $key = 'verify-email-resend:'.$user->id;
        if ($limiter->tooManyAttempts($key, 1)) {
            $this->error = __('auth.verify_email_resend_throttled', [
                'seconds' => $limiter->availableIn($key),
            ]);

            return;
        }
        $limiter->hit($key, 60);

        $max = (10 ** CreateNewUser::VERIFICATION_CODE_LENGTH) - 1;
        $code = str_pad((string) random_int(0, $max), CreateNewUser::VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);
        $user->forceFill([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(CreateNewUser::VERIFICATION_CODE_TTL_MINUTES),
        ])->save();

        dispatch(function () use ($user, $code) {
            $user->notify(new WelcomeWithVerificationCode($code));
        })->afterResponse();

        $this->error = null;
        $this->info = __('auth.verify_email_resend_sent');
    }
};
?>

<div>
    <div class="mb-8">
        <h1 id="verify-email-heading" class="font-heading text-3xl text-heading mb-1.5">
            {{ __('auth.verify_email_heading') }}
        </h1>
        <p class="text-sm text-subtle">
            {!! __('auth.verify_email_subtitle', ['email' => '<strong class="text-body">'.e(auth()->user()->email).'</strong>']) !!}
        </p>
    </div>

    <form wire:submit="verify" aria-labelledby="verify-email-heading" class="space-y-5" novalidate>
        <div>
            <label for="code" class="block text-sm font-medium text-subtle mb-2">
                {{ __('auth.verify_email_input_aria') }}
            </label>
            <input
                id="code"
                type="text"
                wire:model="code"
                inputmode="numeric"
                pattern="[0-9]{{ '{' . CreateNewUser::VERIFICATION_CODE_LENGTH . '}' }}"
                maxlength="{{ CreateNewUser::VERIFICATION_CODE_LENGTH }}"
                autocomplete="one-time-code"
                required
                autofocus
                placeholder="{{ str_repeat('·', CreateNewUser::VERIFICATION_CODE_LENGTH) }}"
                class="w-full px-4 py-4 text-center text-2xl font-semibold tracking-[0.5em] rounded-md bg-white border border-dark/15 text-body placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-colors duration-150"
            />
        </div>

        <p class="text-center text-xs text-subtle">
            {{ __('auth.verify_email_expires_at', [
                'time' => auth()->user()->email_verification_code_expires_at?->format('H:i') ?? '—',
            ]) }}
        </p>

        @if ($error)
            <p role="alert" class="text-sm text-danger">{{ $error }}</p>
        @endif
        @if ($info)
            <p role="status" class="text-sm text-success">{{ $info }}</p>
        @endif

        <x-cta type="submit" size="lg" class="w-full min-h-11">
            <span wire:loading.remove wire:target="verify">{{ __('auth.verify_email_submit') }}</span>
            <span wire:loading wire:target="verify">{{ __('auth.verify_email_submitting') }}</span>
        </x-cta>
    </form>

    <p class="text-sm text-subtle mt-6">
        <button
            type="button"
            wire:click="resend"
            class="text-body font-medium underline underline-offset-2 hover:text-accent transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm cursor-pointer disabled:opacity-50"
            wire:loading.attr="disabled"
            wire:target="resend"
        >
            {{ __('auth.verify_email_resend') }}
        </button>
    </p>
</div>
