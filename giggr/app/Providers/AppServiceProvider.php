<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('localeRoute', function (string $name, array $params = []): string {
            $locale  = app()->getLocale();
            $default = config('app.locale');
            return $locale !== $default
                ? route("{$locale}.{$name}", $params)
                : route($name, $params);
        });
    }
}
