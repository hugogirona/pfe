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

});
