<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Bases\ModelObserver;
use Arcanesoft\Contracts\Auth\Models\Permission;

/**
 * Class     PermissionObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionObserver extends ModelObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    public function creating(Permission $permission)
    {
        $this->event->fire('auth.permissions.creating', compact('permission'));
    }

    public function created(Permission $permission)
    {
        $this->event->fire('auth.permissions.created', compact('permission'));
    }

    public function updating(Permission $permission)
    {
        $this->event->fire('auth.permissions.updating', compact('permission'));
    }

    public function updated(Permission $permission)
    {
        $this->event->fire('auth.permissions.updated', compact('permission'));
    }

    public function saving(Permission $permission)
    {
        $this->event->fire('auth.permissions.saving', compact('permission'));
    }

    public function saved(Permission $permission)
    {
        $this->event->fire('auth.permissions.saved', compact('permission'));
    }

    public function deleting(Permission $permission)
    {
        $permission->roles()->detach();

        $this->event->fire('auth.permissions.deleting', compact('permission'));
    }

    public function deleted(Permission $permission)
    {
        $this->event->fire('auth.permissions.deleted', compact('permission'));
    }
}
