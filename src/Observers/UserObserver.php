<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Bases\ModelObserver;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     UserObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserObserver extends ModelObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function creating(User $user)
    {
        if (UserConfirmator::isEnabled()) {
            $user->confirmation_code = UserConfirmator::generateCode();
        }

        $this->event->fire('auth.users.creating', compact('user'));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function created(User $user)
    {
        $this->event->fire('auth.users.created', compact('user'));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function updating(User $user)
    {
        $this->event->fire('auth.users.updating', compact('user'));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function updated(User $user)
    {
        $this->event->fire('auth.users.updated', compact('user'));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function saving(User $user)
    {
        $this->event->fire('auth.users.saving', compact('user'));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function saved(User $user)
    {
        $this->event->fire('auth.users.saved', compact('user'));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function deleting(User $user)
    {
        if ($user->isAdmin()) return false;

        if ($user->isForceDeleting()) {
            $user->roles()->detach();
        }

        $this->event->fire('auth.users.deleting', compact('user'));

        return true;
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function deleted(User $user)
    {
        $this->event->fire('auth.users.deleted', compact('user'));
    }

    /**
     * Eloquent 'restoring' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function restoring(User $user)
    {
        $this->event->fire('auth.users.restoring', compact('user'));
    }

    /**
     * Eloquent 'restored' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return mixed
     */
    public function restored(User $user)
    {
        $this->event->fire('auth.users.restored', compact('user'));
    }
}
