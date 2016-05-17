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
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function creating(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.creating', compact('group'));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function created(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.created', compact('group'));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function updating(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.updating', compact('group'));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function updated(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.updated', compact('group'));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function saving(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.saving', compact('group'));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function saved(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.saved', compact('group'));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function deleting(PermissionsGroup $group)
    {
        $group->detachAllPermissions(false);

        $this->event->fire('auth.permission-groups.deleting', compact('group'));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     *
     * @return mixed
     */
    public function deleted(PermissionsGroup $group)
    {
        $this->event->fire('auth.permission-groups.deleted', compact('group'));
    }
}
