<?php

use App\Models\City;
use App\Models\Profile;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Livewire\Livewire;

function validRegistrationInput(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'hugo@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'birth_date' => '1998-06-15',
        'rgpd' => true,
    ], $overrides);
}

it('creates a profile automatically when a new user registers', function () {
    $user = app(CreatesNewUsers::class)->create(validRegistrationInput());

    expect(Profile::where('user_id', $user->id)->exists())->toBeTrue();
});

it('stores first_name and last_name separately on the user', function () {
    $user = app(CreatesNewUsers::class)->create(validRegistrationInput(['email' => 'hugo2@example.com']));

    expect($user->fresh())
        ->first_name->toBe('Hugo')
        ->and($user->fresh()->last_name)->toBe('Test');
});

it('stores the birth date on the profile', function () {
    $user = app(CreatesNewUsers::class)->create(validRegistrationInput(['birth_date' => '1990-01-31']));

    expect($user->profile->birth_date->format('Y-m-d'))->toBe('1990-01-31');
});

it('allows registration without a birth date', function () {
    $input = validRegistrationInput();
    unset($input['birth_date']);

    $user = app(CreatesNewUsers::class)->create($input);

    expect($user->profile->birth_date)->toBeNull();
});

it('stores the city on the profile when one is provided', function () {
    $city = City::factory()->create();

    $user = app(CreatesNewUsers::class)->create(validRegistrationInput(['city_id' => $city->id]));

    expect($user->profile->city_id)->toBe($city->id);
});

it('allows registration without a city', function () {
    $user = app(CreatesNewUsers::class)->create(validRegistrationInput());

    expect($user->profile->city_id)->toBeNull();
});

it('rejects a city that does not exist', function () {
    expect(fn () => app(CreatesNewUsers::class)->create(
        validRegistrationInput(['city_id' => 999999])
    ))->toThrow(ValidationException::class);
});

it('rejects a birth date in the future', function () {
    expect(fn () => app(CreatesNewUsers::class)->create(
        validRegistrationInput(['birth_date' => now()->addDay()->format('Y-m-d')])
    ))->toThrow(ValidationException::class);
});

it('rejects a non-ISO birth date format', function () {
    expect(fn () => app(CreatesNewUsers::class)->create(
        validRegistrationInput(['birth_date' => '15/06/1998'])
    ))->toThrow(ValidationException::class);
});

it('renders an ISO date input for the birth date on the register form', function () {
    Livewire::test('pages::auth.register')
        ->assertSeeHtml('name="birth_date"')
        ->assertSeeHtml('type="date"');
});

it('renders a privacy-policy consent checkbox on the register form', function () {
    Livewire::test('pages::auth.register')
        ->assertSeeHtml('name="rgpd"');
});

it('requires accepting the privacy policy', function () {
    $input = validRegistrationInput();
    unset($input['rgpd']);

    expect(fn () => app(CreatesNewUsers::class)->create($input))
        ->toThrow(ValidationException::class);
});

it('rejects registration when the privacy policy is declined', function () {
    expect(fn () => app(CreatesNewUsers::class)->create(validRegistrationInput(['rgpd' => false])))
        ->toThrow(ValidationException::class);
});
