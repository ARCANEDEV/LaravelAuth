<?php namespace Arcanedev\LaravelAuth\Listeners\Permissions;

use Arcanedev\LaravelAuth\Events\Permissions\DeletingPermission;

/**
 * Class     DetachingRoles
 *
 * @package  Arcanedev\LaravelAuth\Listeners\Permissions
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
     * @param  \Arcanedev\LaravelAuth\Events\Permissions\DeletingPermission  $event
     */
    public function handle(DeletingPermission $event)
    {
        $event->permission->roles()->detach();
    }
}
