<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {

    Route::livewire('/', 'pages::home.index')->name('home');
    Route::livewire('/explorer', 'pages::explore.index')->name('explore');
    Route::livewire('/contact', 'pages::contact.index')->name('contact');
    Route::livewire('/register', 'pages::auth.register')->name('register');
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset');

});
