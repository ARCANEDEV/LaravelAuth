<?php namespace Arcanedev\LaravelAuth\Providers;

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

        $observers = [
            'users'              => \Arcanesoft\Contracts\Auth\Models\User::class,
            'roles'              => \Arcanesoft\Contracts\Auth\Models\Role::class,
            'permissions-groups' => \Arcanesoft\Contracts\Auth\Models\PermissionsGroup::class,
            'permissions'        => \Arcanesoft\Contracts\Auth\Models\Permission::class,
        ];

        foreach ($observers as $name => $abstract) {
            $this->observe($name, $abstract);
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Observe the model.
     *
     * @param  string  $name
     * @param  string  $abstract
     */
    private function observe($name, $abstract)
    {
        $this->getModel($abstract)->observe(
            $this->getObserver($name)
        );
    }

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
