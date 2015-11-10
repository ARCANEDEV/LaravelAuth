<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
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
    /**
     * Register the application's event listeners.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        $this->observeModels();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Observe the models.
     */
    private function observeModels()
    {
        User::observe(\Arcanedev\LaravelAuth\Observers\UserObserver::class);
        Role::observe(\Arcanedev\LaravelAuth\Observers\RoleObserver::class);
        Permission::observe(\Arcanedev\LaravelAuth\Observers\PermissionObserver::class);
    }
}
