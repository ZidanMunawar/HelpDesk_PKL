<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmailExcludingDeleted implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if email exists in active (not soft deleted) and verified users
        $existingUser = User::where('email', $value)
            ->whereNotNull('email_verified_at')
            ->first();

        return !$existingUser; // Pass jika tidak ada user verified dengan email ini
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This email is already registered and verified.';
    }
}
