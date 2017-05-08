<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Arcanedev\LaravelAuth\Events\Users as UserEvents;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     UserObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserObserver extends AbstractObserver
{
    /* -----------------------------------------------------------------
     |  Events
     | -----------------------------------------------------------------
     */

    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function creating(User $user)
    {
        if (UserConfirmator::isEnabled())
            $user->confirmation_code = UserConfirmator::generateCode();

        $this->event->dispatch(new UserEvents\CreatingUser($user));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function created(User $user)
    {
        $this->event->dispatch(new UserEvents\CreatedUser($user));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function updating(User $user)
    {
        $this->event->dispatch(new UserEvents\UpdatingUser($user));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function updated(User $user)
    {
        $this->event->dispatch(new UserEvents\UpdatedUser($user));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function saving(User $user)
    {
        $this->event->dispatch(new UserEvents\SavingUser($user));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function saved(User $user)
    {
        $this->event->dispatch(new UserEvents\SavedUser($user));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     *
     * @return bool
     */
    public function deleting(User $user)
    {
        if ($user->isAdmin()) return false;

        if ($user->isForceDeleting()) $user->roles()->detach();

        $this->event->dispatch(new UserEvents\DeletingUser($user));

        return true;
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function deleted(User $user)
    {
        $this->event->dispatch(new UserEvents\DeletedUser($user));
    }

    /**
     * Eloquent 'restoring' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function restoring(User $user)
    {
        $this->event->dispatch(new UserEvents\RestoringUser($user));
    }

    /**
     * Eloquent 'restored' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|User  $user
     */
    public function restored(User $user)
    {
        $this->event->dispatch(new UserEvents\RestoredUser($user));
    }
}
