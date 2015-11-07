<?php namespace Arcanedev\LaravelAuth;

use Arcanedev\Support\PackageServiceProvider as ServiceProvider;

/**
 * Class     LaravelAuthServiceProvider
 *
 * @package  Arcanedev\LaravelAuth
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelAuthServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor  = 'arcanedev';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'laravel-auth';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(__DIR__);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->registerPublishes();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            //
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register all publishable stuff.
     */
    private function registerPublishes()
    {
        $this->publishes([
            $this->getConfigFile() => config_path("{$this->package}.php"),
        ], 'config');

        $this->publishes([
            $this->getBasePath() . DS . 'database/migrations' => database_path('migrations'),
        ], 'migrations');
    }
}
