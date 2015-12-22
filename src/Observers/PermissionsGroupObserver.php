<?php namespace Arcanedev\LaravelAuth\Observers;

use Arcanedev\LaravelAuth\Bases\ModelObserver;
use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     PermissionsGroupObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionsGroupObserver extends ModelObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    public function creating(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.creating', compact('group'));
    }

    public function created(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.created', compact('group'));
    }

    public function updating(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.updating', compact('group'));
    }

    public function updated(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.updated', compact('group'));
    }

    public function saving(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.saving', compact('group'));
    }

    public function saved(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.saved', compact('group'));
    }

    public function deleting(PermissionsGroup $group)
    {
        $group->detachAllPermissions(false);

        $this->event->fire('auth.permission-groups.deleting', compact('group'));
    }

    public function deleted(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.deleted', compact('group'));
    }
}
