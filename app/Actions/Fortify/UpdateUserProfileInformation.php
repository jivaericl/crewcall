<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'emergency_contact_first_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_last_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'emergency_contact_email' => ['nullable', 'email', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'timezone' => $input['timezone'] ?? 'UTC',
                'emergency_contact_first_name' => $input['emergency_contact_first_name'] ?? null,
                'emergency_contact_last_name' => $input['emergency_contact_last_name'] ?? null,
                'emergency_contact_relationship' => $input['emergency_contact_relationship'] ?? null,
                'emergency_contact_phone' => $input['emergency_contact_phone'] ?? null,
                'emergency_contact_email' => $input['emergency_contact_email'] ?? null,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'timezone' => $input['timezone'] ?? 'UTC',
            'emergency_contact_first_name' => $input['emergency_contact_first_name'] ?? null,
            'emergency_contact_last_name' => $input['emergency_contact_last_name'] ?? null,
            'emergency_contact_relationship' => $input['emergency_contact_relationship'] ?? null,
            'emergency_contact_phone' => $input['emergency_contact_phone'] ?? null,
            'emergency_contact_email' => $input['emergency_contact_email'] ?? null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
