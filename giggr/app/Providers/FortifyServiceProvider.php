<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RegisterResponse::class, fn () => new class implements RegisterResponse
        {
            public function toResponse($request): mixed
            {
                $supported = array_keys(config('laravellocalization.supportedLocales', []));
                $locale = app()->getLocale();

                if ($referer = $request->header('referer')) {
                    $first = explode('/', trim((string) parse_url($referer, PHP_URL_PATH), '/'))[0] ?? '';
                    if (in_array($first, $supported, strict: true)) {
                        $locale = $first;
                    }
                }

                app()->setLocale($locale);

                return redirect()->route('profile', ['id' => auth()->user()->profile->id]);
            }
        });
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
