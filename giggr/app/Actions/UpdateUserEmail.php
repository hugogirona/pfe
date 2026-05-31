<?php

namespace App\Actions;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateUserEmail
{
    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'current_password.current_password' => __('settings.current_password_mismatch'),
        ])->after(function ($validator) use ($user, $input): void {
            if (($input['email'] ?? null) === $user->email) {
                $validator->errors()->add('email', __('settings.email_unchanged'));
            }
        })->validate();

        $max = (10 ** CreateNewUser::VERIFICATION_CODE_LENGTH) - 1;
        $code = str_pad((string) random_int(0, $max), CreateNewUser::VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);

        $user->forceFill([
            'email' => $input['email'],
            'email_verified_at' => null,
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(CreateNewUser::VERIFICATION_CODE_TTL_MINUTES),
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
