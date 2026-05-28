<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/site.webmanifest', function () {
    return response()->json([
        'name' => 'Giggr.',
        'short_name' => 'Giggr.',
        'icons' => [
            [
                'src' => Vite::asset('resources/favicon/web-app-manifest-192x192.png'),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ],
            [
                'src' => Vite::asset('resources/favicon/web-app-manifest-512x512.png'),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ],
        ],
        'theme_color' => '#ffffff',
        'background_color' => '#ffffff',
        'display' => 'standalone',
    ])->header('Content-Type', 'application/manifest+json');
})->name('site.webmanifest');

Route::post('/contact', ContactController::class)->name('contact.submit');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {

    Route::livewire('/', 'pages::home.index')->name('home');
    Route::livewire(LaravelLocalization::transRoute('routes.explore'), 'pages::explore.index')
        ->name('explore')
        ->where('tab', 'profils|annonces|profiles|listings');
    Route::livewire(LaravelLocalization::transRoute('routes.profile'), 'pages::profile.index')->name('profile')->middleware(['auth', 'verified']);
    Route::livewire(LaravelLocalization::transRoute('routes.announcement'), 'pages::announcement.index')->name('announcement')->middleware(['auth', 'verified']);
    Route::livewire(LaravelLocalization::transRoute('routes.contact'), 'pages::contact.index')->name('contact');
    Route::livewire(LaravelLocalization::transRoute('routes.settings_account'), 'pages::settings.account')->name('settings.account')->middleware(['auth', 'verified']);
    Route::livewire(LaravelLocalization::transRoute('routes.register'), 'pages::auth.register')->name('register');
    Route::livewire(LaravelLocalization::transRoute('routes.login'), 'pages::auth.login')->name('login');
    Route::livewire(LaravelLocalization::transRoute('routes.password_request'), 'pages::auth.forgot-password')->name('password.request');
    Route::livewire(LaravelLocalization::transRoute('routes.password_reset'), 'pages::auth.reset-password')->name('password.reset');
    Route::livewire(LaravelLocalization::transRoute('routes.verification_notice'), 'pages::auth.verify-email')->name('verification.notice')->middleware('auth');

});
