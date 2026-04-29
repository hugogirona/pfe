<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password'   => $this->passwordRules(),
        ], [
            'email.unique' => __('auth.email_taken', ['url' => route('login')]),
        ])->validate();

        return User::create([
            'name'     => trim($input['first_name'].' '.$input['last_name']),
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
