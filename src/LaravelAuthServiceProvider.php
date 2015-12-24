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

        $this->bindModels();

        if ($this->app['config']->get('laravel-auth.use-observers', false)) {
            $this->app->register(Providers\EventServiceProvider::class);
        }
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->registerPublishes();
        $this->registerBladeDirectives();
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
     * Binding the models with the contracts.
     */
    private function bindModels()
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app['config'];

        $this->bind(
            \Arcanesoft\Contracts\Auth\Models\User::class,
            $config->get('laravel-auth.users.model')
        );

        $this->bind(
            \Arcanesoft\Contracts\Auth\Models\Role::class,
            $config->get('laravel-auth.roles.model')
        );

        $this->bind(
            \Arcanesoft\Contracts\Auth\Models\Permission::class,
            $config->get('laravel-auth.permissions.model')
        );

        $this->bind(
            \Arcanesoft\Contracts\Auth\Models\PermissionsGroup::class,
            $config->get('laravel-auth.permissions-groups.model')
        );
    }

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

        $this->publishes([
            $this->getBasePath() . DS . 'database/factories' => database_path('factories'),
        ], 'factories');
    }

    /**
     * Register blade directives
     */
    private function registerBladeDirectives()
    {
        // Coming soon
    }
}
