<?php

use App\Models\User;

it('uppercases the first letter of the first and last name on save', function () {
    $user = User::factory()->create([
        'first_name' => 'hugo',
        'last_name' => 'girona',
    ]);

    expect($user->fresh()->first_name)->toBe('Hugo')
        ->and($user->fresh()->last_name)->toBe('Girona');
});

it('leaves an already capitalized name unchanged', function () {
    $user = User::factory()->create([
        'first_name' => 'Hugo',
        'last_name' => 'Girona',
    ]);

    expect($user->fresh()->first_name)->toBe('Hugo')
        ->and($user->fresh()->last_name)->toBe('Girona');
});

it('preserves internal casing while capitalizing only the first letter', function () {
    $user = User::factory()->create([
        'first_name' => 'mcCartney',
        'last_name' => 'deSouza',
    ]);

    expect($user->fresh()->first_name)->toBe('McCartney')
        ->and($user->fresh()->last_name)->toBe('DeSouza');
});

it('uppercases accented first letters in a multibyte-safe way', function () {
    $user = User::factory()->create([
        'first_name' => 'élodie',
        'last_name' => 'éric',
    ]);

    expect($user->fresh()->first_name)->toBe('Élodie')
        ->and($user->fresh()->last_name)->toBe('Éric');
});

it('trims surrounding whitespace before capitalizing', function () {
    $user = User::factory()->create([
        'first_name' => '  hugo  ',
        'last_name' => "\tgirona\n",
    ]);

    expect($user->fresh()->first_name)->toBe('Hugo')
        ->and($user->fresh()->last_name)->toBe('Girona');
});
