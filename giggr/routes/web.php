<?php

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

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {

    Route::livewire('/', 'pages::home.index')->name('home');
    Route::livewire('/explorer', 'pages::explore.index')->name('explore');
    Route::livewire('/profil/{id}', 'pages::profile.index')->name('profile')->middleware(['auth', 'verified']);
    Route::livewire('/annonces/{id}', 'pages::announcement.index')->name('announcement')->middleware(['auth', 'verified']);
    Route::livewire('/contact', 'pages::contact.index')->name('contact');
    Route::livewire('/parametres/compte', 'pages::settings.account')->name('settings.account')->middleware(['auth', 'verified']);
    Route::livewire('/register', 'pages::auth.register')->name('register');
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset');
    Route::livewire('/verify-email', 'pages::auth.verify-email')->name('verification.notice')->middleware('auth');

});
