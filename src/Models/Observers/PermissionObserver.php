<?php namespace Arcanedev\LaravelAuth\Models\Observers;

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
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function creating(Permission $permission)
    {
        $this->event->fire('auth.permissions.creating', compact('permission'));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function created(Permission $permission)
    {
        $this->event->fire('auth.permissions.created', compact('permission'));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function updating(Permission $permission)
    {
        $this->event->fire('auth.permissions.updating', compact('permission'));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function updated(Permission $permission)
    {
        $this->event->fire('auth.permissions.updated', compact('permission'));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function saving(Permission $permission)
    {
        $this->event->fire('auth.permissions.saving', compact('permission'));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function saved(Permission $permission)
    {
        $this->event->fire('auth.permissions.saved', compact('permission'));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function deleting(Permission $permission)
    {
        $permission->roles()->detach();

        $this->event->fire('auth.permissions.deleting', compact('permission'));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function deleted(Permission $permission)
    {
        $this->event->fire('auth.permissions.deleted', compact('permission'));
    }
}
