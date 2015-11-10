<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Models\Permission;

/**
 * Class     PermissionObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionObserver
{
    public function creating(Permission $permission)
    {
        //
    }

    public function created(Permission $permission)
    {
        //
    }

    public function saving(Permission $permission)
    {
        //
    }

    public function saved(Permission $permission)
    {
        //
    }

    public function deleting(Permission $permission)
    {
        $permission->roles()->detach();
    }

    public function deleted(Permission $user)
    {
        //
    }
}
