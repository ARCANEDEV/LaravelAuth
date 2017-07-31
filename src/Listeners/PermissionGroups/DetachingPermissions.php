<?php namespace Arcanedev\LaravelAuth\Listeners\PermissionGroups;

use Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletingPermissionsGroup;

/**
 * Class     DetachingPermissions
 *
 * @package  Arcanedev\LaravelAuth\Listeners\PermissionGroups
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingPermissions
{
    /* -----------------------------------------------------------------
     |  Main Method
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletingPermissionsGroup  $event
     */
    public function handle(DeletingPermissionsGroup $event)
    {
        $event->group->detachAllPermissions(false);
    }
}
