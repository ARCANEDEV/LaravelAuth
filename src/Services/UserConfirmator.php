<?php namespace Arcanedev\LaravelAuth\Services;

use Arcanedev\LaravelAuth\Events\Users\ConfirmedUser;
use Arcanedev\LaravelAuth\Events\Users\ConfirmingUser;
use Arcanesoft\Contracts\Auth\Models\User as UserContract;
use Illuminate\Support\Str;

/**
 * Class     UserConfirmator
 *
 * @package  Arcanedev\LaravelAuth\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserConfirmator
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Generate the confirmation code.
     *
     * @return string
     */
    public static function generateCode()
    {
        return Str::random(self::getLength());
    }

    /**
     * Confirm user account.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|\Arcanedev\LaravelAuth\Models\User  $user
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     */
    public function confirm(UserContract $user)
    {
        event(new ConfirmingUser($user));

        $user->forceFill([
            'confirmation_code' => null,
            'confirmed_at'      => now(),
        ])->save();

        event(new ConfirmedUser($user));

        return $user;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
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
