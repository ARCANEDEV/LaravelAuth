<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\LaravelAuth\Observers;
use Arcanedev\Support\Providers\EventServiceProvider as ServiceProvider;
use Arcanesoft\Contracts\Auth\Models;
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

        $this->observeUserModel();
        $this->observeRoleModel();
        $this->observePermissionsGroupModel();
        $this->observePermissionModel();
    }

    private function observeUserModel()
    {
        $observer = $this->getObserver('users');

        $this->getModel(Models\User::class)->observe($observer);
    }

    private function observeRoleModel()
    {
        $observer = $this->getObserver('roles');

        $this->getModel(Models\Role::class)->observe($observer);
    }

    private function observePermissionsGroupModel()
    {
        $observer = $this->getObserver('permissions-groups');

        $this->getModel(Models\PermissionsGroup::class)->observe($observer);
    }

    private function observePermissionModel()
    {
        $observer = $this->getObserver('permissions');

        $this->getModel(Models\Permission::class)->observe($observer);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the concrete model.
     *
     * @param  string  $abstract
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getModel($abstract)
    {
        return $this->app->make($abstract);
    }

    /**
     * Get the observer class.
     *
     * @param  string  $key
     *
     * @return string
     */
    private function getObserver($key)
    {
        /** @var  \Illuminate\Contracts\Config\Repository  $config */
        $config = $this->app['config'];

        return $config->get("laravel-auth.{$key}.observer");
    }
}
