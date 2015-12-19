<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\LaravelAuth\Models;
use Arcanedev\LaravelAuth\Observers;
use Arcanedev\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class     EventServiceProvider
 *
 * @package  Arcanedev\LaravelAuth\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class EventServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the application's event listeners.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        Models\User::observe(Observers\UserObserver::class);
        Models\Role::observe(Observers\RoleObserver::class);
        Models\PermissionsGroup::observe(Observers\PermissionsGroupObserver::class);
        Models\Permission::observe(Observers\PermissionObserver::class);
    }
}
