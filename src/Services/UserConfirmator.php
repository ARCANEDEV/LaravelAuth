<?php namespace Arcanedev\LaravelAuth\Services;

use Arcanedev\LaravelAuth\Events\Users\ConfirmedUser;
use Arcanedev\LaravelAuth\Events\Users\ConfirmingUser;
use Arcanesoft\Contracts\Auth\Models\User as UserContract;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        return Str::random(self::getLength());
    }

    /**
     * Confirm user account.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     */
    public function confirm(UserContract $user)
    {
        event(new ConfirmingUser($user));

        $user->setAttribute('is_confirmed',      true);
        $user->setAttribute('confirmation_code', null);
        $user->setAttribute('confirmed_at',      Carbon::now());
        $user->save();

        event(new ConfirmedUser($user));

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
