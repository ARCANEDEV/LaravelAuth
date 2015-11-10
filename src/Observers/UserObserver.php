<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Services\UserConfirmator;

/**
 * Class     UserObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserObserver
{
    public function creating(User $user)
    {
        if (UserConfirmator::isEnabled()) {
            $user->confirmation_code = UserConfirmator::generateCode();
        }
    }

    public function created(User $user)
    {
        //
    }

    public function saving(User $user)
    {
        //
    }

    public function saved(User $user)
    {
        //
    }

    public function deleting(User $user)
    {
        if ($user->isAdmin()) {
            return false;
        }

        if ($user->trashed()) {
            $user->roles()->detach();
        }

        return true;
    }

    public function deleted(User $user)
    {
        //
    }
}
