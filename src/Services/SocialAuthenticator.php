<?php namespace Arcanedev\LaravelAuth\Services;
use Illuminate\Support\Collection;

/**
 * Class     SocialAuthenticator
 *
 * @package  Arcanedev\LaravelAuth\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SocialAuthenticator
{
    /**
     * Check if social authentication is enabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return config('laravel-auth.socialite.enabled', false);
    }

    /**
     * Get the supported drivers.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function drivers()
    {
        return Collection::make(config('laravel-auth.socialite.drivers', []));
    }

    /**
     * Get the enabled drivers.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function enabledDrivers()
    {
        return static::drivers()->filter(function ($driver) {
            return $driver['enabled'];
        });
    }

    /**
     * Check if the given driver is supported.
     *
     * @param  string  $driver
     *
     * @return bool
     */
    public static function isSupported($driver)
    {
        return static::enabledDrivers()->has($driver);
    }
}
