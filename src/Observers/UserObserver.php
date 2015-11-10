<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Bases\ModelObserver;
use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Services\UserConfirmator;

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
    public function creating(User $user)
    {
        if (UserConfirmator::isEnabled()) {
            $user->confirmation_code = UserConfirmator::generateCode();
        }

        $this->event->fire('auth.users.creating', compact('user'));
    }

    public function created(User $user)
    {
        $this->event->fire('auth.users.created', compact('user'));
    }

    public function updating(User $user)
    {
        $this->event->fire('auth.users.updating', compact('user'));
    }

    public function updated(User $user)
    {
        $this->event->fire('auth.users.updated', compact('user'));
    }

    public function saving(User $user)
    {
        $this->event->fire('auth.users.saving', compact('user'));
    }

    public function saved(User $user)
    {
        $this->event->fire('auth.users.saved', compact('user'));
    }

    public function deleting(User $user)
    {
        if ($user->isAdmin()) {
            return false;
        }

        if ($user->trashed()) {
            $user->roles()->detach();
        }

        $this->event->fire('auth.users.deleting', compact('user'));

        return true;
    }

    public function deleted(User $user)
    {
        $this->event->fire('auth.users.deleted', compact('user'));
    }

    public function restoring(User $user)
    {
        $this->event->fire('auth.users.restoring', compact('user'));
    }

    public function restored(User $user)
    {
        $this->event->fire('auth.users.restored', compact('user'));
    }
}
