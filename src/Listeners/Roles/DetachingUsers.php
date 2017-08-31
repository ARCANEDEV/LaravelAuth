<?php namespace Arcanedev\LaravelAuth\Listeners\Roles;

use Arcanedev\LaravelAuth\Events\Roles\DeletingRole;

/**
 * Class     DetachingUsers
 *
 * @package  Arcanedev\LaravelAuth\Listeners\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingUsers
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelAuth\Events\Roles\DeletingRole  $event
     *
     * @return boolean
     */
    public function handle(DeletingRole $event)
    {
        $event->role->detachAllUsers(false);
    }
}
