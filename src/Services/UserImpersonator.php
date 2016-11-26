<?php namespace Arcanedev\LaravelAuth\Services;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     UserImpersonator
 *
 * @package  Arcanedev\LaravelAuth\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserImpersonator
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Start the user impersonation.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public static function start(User $user)
    {
        if ( ! $user->canBeImpersonated()) return false;

        session()->put(self::getKey(), $user->id);

        return true;
    }

    /**
     * Stop the user impersonation.
     */
    public static function stop()
    {
        session()->forget(self::getKey());
    }

    /**
     * Check if the impersonation is ongoing.
     *
     * @return bool
     */
    public static function isImpersonating()
    {
        return session()->has(self::getKey());
    }

    /**
     * Check if the impersonation is enabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return config('laravel-auth.impersonation.enabled', false);
    }

    /**
     * Get the session key.
     *
     * @return string
     */
    public static function getKey()
    {
        return config('laravel-auth.impersonation.key', 'impersonate');
    }

    /**
     * Get the user id.
     *
     * @return mixed
     */
    public static function getUserId()
    {
        return session()->get(self::getKey(), null);
    }
}
