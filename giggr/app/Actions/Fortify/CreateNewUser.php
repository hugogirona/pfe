<?php

namespace App\Actions\Fortify;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Throwable;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public const int VERIFICATION_CODE_LENGTH = 6;

    public const int VERIFICATION_CODE_TTL_MINUTES = 10;

    /**
     * @throws Throwable
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'rgpd' => ['accepted'],
        ], [
            'email.unique' => __('auth.email_taken', ['url' => route('login')]),
            'rgpd.accepted' => __('auth.register_rgpd_required'),
        ])->validate();

        $max = (10 ** self::VERIFICATION_CODE_LENGTH) - 1;
        $code = str_pad((string) random_int(0, $max), self::VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($input, $code): User {
            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'email_verification_code' => $code,
                'email_verification_code_expires_at' => now()->addMinutes(self::VERIFICATION_CODE_TTL_MINUTES),
            ]);

            Profile::create([
                'user_id' => $user->id,
                'birth_date' => $input['birth_date'] ?? null,
            ]);

            return $user;
        });
    }
}
