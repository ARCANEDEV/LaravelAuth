<?php namespace Arcanedev\LaravelAuth\Providers;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
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
        $this->registerUserModelEvents();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register user model events
     */
    private function registerUserModelEvents()
    {
        User::creating(function (User $user) {
            $user->confirmation_code = UserConfirmator::generateCode();
        });

        User::created(function (User $user) {
            //
        });

        User::deleting(function (User $user) {
            if ($user->trashed()) {
                $user->detachAllRoles();
            }
        });

        User::deleted(function (User $user) {
            //
        });
    }
}
