<?php

namespace Tests\Feature\Smoke;

use Illuminate\Support\Facades\DB;

it('starts every test with an empty users table', function () {
    expect(DB::table('users')->count())->toBe(0);
});

it('does not leak data across tests', function () {
    DB::table('users')->insert([
        'first_name' => 'Leaky',
        'last_name' => 'User',
        'email' => 'leak@example.com',
        'password' => 'x',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    expect(DB::table('users')->count())->toBe(1);
});

it('confirms previous insert was rolled back', function () {
    expect(DB::table('users')->count())->toBe(0);
});
