<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Models\Role;

/**
 * Class     RoleObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleObserver
{
    public function creating(Role $role)
    {
        //
    }

    public function created(Role $role)
    {
        //
    }

    public function saving(Role $role)
    {
        //
    }

    public function saved(Role $role)
    {
        //
    }

    public function deleting(Role $role)
    {
        $role->users()->detach();
        $role->permissions()->detach();
    }

    public function deleted(Role $role)
    {
        //
    }
}
