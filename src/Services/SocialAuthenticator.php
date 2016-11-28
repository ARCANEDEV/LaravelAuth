<?php namespace Arcanedev\LaravelAuth\Services;

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
     * @return array
     */
    public static function drivers()
    {
        $drivers = array_filter(config('laravel-auth.socialite.drivers', []));

        return array_keys($drivers);
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
        return in_array($driver, static::drivers());
    }
}
