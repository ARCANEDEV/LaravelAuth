<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class     EventServiceProvider
 *
 * @package  Arcanedev\LaravelAuth\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class EventServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    
    /**
     * Register the application's event listeners.
     */
    public function boot()
    {
        $this->listen = array_filter(config('laravel-auth.events.listeners'));

        parent::boot();
    }
}
