<?php namespace Arcanedev\LaravelAuth;

/**
 * Class     Auth
 *
 * @package  Arcanedev\LaravelAuth
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Auth
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * Indicates if migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Publish the migrations.
     */
    public static function publishMigrations()
    {
        static::$runsMigrations = false;
    }
}
