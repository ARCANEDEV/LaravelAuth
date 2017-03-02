<?php namespace Arcanedev\LaravelAuth;

use Arcanedev\Support\PackageServiceProvider as ServiceProvider;
use Arcanesoft\Contracts\Auth\Models as AuthContracts;

/**
 * Class     LaravelAuthServiceProvider
 *
 * @package  Arcanedev\LaravelAuth
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelAuthServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'laravel-auth';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->bindModels();

        if ($this->config()->get('laravel-auth.observers.enabled', false))
            $this->registerProvider(Providers\EventServiceProvider::class);
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->publishConfig();
        $this->publishFactories();

        Auth::$runsMigrations ? $this->loadMigrations() : $this->publishMigrations();
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

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    /**
     * Binding the models with the contracts.
     */
    private function bindModels()
    {
        $bindings = [
            'users'              => AuthContracts\User::class,
            'roles'              => AuthContracts\Role::class,
            'permissions'        => AuthContracts\Permission::class,
            'permissions-groups' => AuthContracts\PermissionsGroup::class,
        ];

        foreach ($bindings as $key => $contract) {
            $this->bind($contract, $this->config()->get("laravel-auth.$key.model"));
        }
    }
}
