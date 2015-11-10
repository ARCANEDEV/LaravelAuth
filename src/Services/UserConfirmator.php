<?php namespace Arcanedev\LaravelAuth\Services;

use Arcanedev\LaravelAuth\Contracts\User;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     UserConfirmator
 *
 * @package  Arcanedev\LaravelAuth\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserConfirmator
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Generate the confirmation code.
     *
     * @return string
     */
    public static function generateCode()
    {
        return str_random(self::getLength());
    }

    /**
     * Confirm user account.
     *
     * @param  User  $user
     *
     * @return User
     */
    public function confirm(User $user)
    {
        event('auth.users.confirming', compact('user'));

        $user->is_confirmed      = true;
        $user->confirmation_code = null;
        $user->confirmed_at      = \Carbon\Carbon::now();
        $user->save();

        event('auth.users.confirmed', compact('user'));

        return $user;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if the confirmator is enabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return config('laravel-auth.user-confirmation.enabled', false);
    }

    /**
     * Get confirmation code length.
     *
     * @return int
     */
    public static function getLength()
    {
        return config('laravel-auth.user-confirmation.length', 30);
    }
}
