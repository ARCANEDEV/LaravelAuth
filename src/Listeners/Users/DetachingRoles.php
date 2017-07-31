<?php namespace Arcanedev\LaravelAuth\Listeners\Users;

use Arcanedev\LaravelAuth\Events\Users\DeletingUser;

/**
 * Class     DetachingRoles
 *
 * @package  Arcanedev\LaravelAuth\Listeners\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingRoles
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelAuth\Events\Users\DeletingUser  $event
     *
     * @return boolean
     */
    public function handle(DeletingUser $event)
    {
        if ($event->user->isAdmin())
            return false;

        if ($event->user->isForceDeleting())
            $event->user->detachAllRoles(false);

        return true;
    }
}
