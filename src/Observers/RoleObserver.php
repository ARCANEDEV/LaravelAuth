<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Bases\ModelObserver;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     RoleObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleObserver extends ModelObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    public function creating(Role $role)
    {
        $this->event->fire('auth.roles.creating', compact('role'));
    }

    public function created(Role $role)
    {
        $this->event->fire('auth.roles.created', compact('role'));
    }

    public function updating(Role $role)
    {
        $this->event->fire('auth.roles.updating', compact('role'));
    }

    public function updated(Role $role)
    {
        $this->event->fire('auth.roles.updated', compact('role'));
    }

    public function saving(Role $role)
    {
        $this->event->fire('auth.roles.saving', compact('role'));
    }

    public function saved(Role $role)
    {
        $this->event->fire('auth.roles.saved', compact('role'));
    }

    public function deleting(Role $role)
    {
        $role->users()->detach();
        $role->permissions()->detach();

        $this->event->fire('auth.roles.deleting', compact('role'));
    }

    public function deleted(Role $role)
    {
        $this->event->fire('auth.roles.deleted', compact('role'));
    }
}
