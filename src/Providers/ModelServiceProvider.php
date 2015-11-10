<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Observers\PermissionObserver;
use Arcanedev\LaravelAuth\Observers\RoleObserver;
use Arcanedev\LaravelAuth\Observers\UserObserver;
use Arcanedev\Support\ServiceProvider;

/**
 * Class     ModelServiceProvider
 *
 * @package  Arcanedev\LaravelAuth\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ModelServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        User::observe(new UserObserver);
        Role::observe(new RoleObserver);
        Permission::observe(new PermissionObserver);
    }
}
