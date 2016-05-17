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
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function creating(Role $role)
    {
        $this->event->fire('auth.roles.creating', compact('role'));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function created(Role $role)
    {
        $this->event->fire('auth.roles.created', compact('role'));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function updating(Role $role)
    {
        $this->event->fire('auth.roles.updating', compact('role'));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function updated(Role $role)
    {
        $this->event->fire('auth.roles.updated', compact('role'));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function saving(Role $role)
    {
        $this->event->fire('auth.roles.saving', compact('role'));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function saved(Role $role)
    {
        $this->event->fire('auth.roles.saved', compact('role'));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function deleting(Role $role)
    {
        $role->users()->detach();
        $role->permissions()->detach();

        $this->event->fire('auth.roles.deleting', compact('role'));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     *
     * @return mixed
     */
    public function deleted(Role $role)
    {
        $this->event->fire('auth.roles.deleted', compact('role'));
    }
}
