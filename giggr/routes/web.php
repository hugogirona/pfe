<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home.index')->name('home');

Route::prefix('en')->name('en.')->group(function () {
    Route::livewire('/', 'pages::home.index')->name('home');
});
