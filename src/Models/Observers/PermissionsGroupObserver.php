<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Arcanedev\LaravelAuth\Events\PermissionsGroups as PermissionsGroupEvents;
use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     PermissionsGroupObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionsGroupObserver extends AbstractObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function creating(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\CreatingPermissionsGroup($group));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function created(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\CreatedPermissionsGroup($group));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function updating(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\UpdatingPermissionsGroup($group));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function updated(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\UpdatedPermissionsGroup($group));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function saving(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\SavingPermissionsGroup($group));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function saved(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\SavedPermissionsGroup($group));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function deleting(PermissionsGroup $group)
    {
        $group->detachAllPermissions(false);

        $this->event->dispatch(new PermissionsGroupEvents\DeletingPermissionsGroup($group));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\PermissionsGroup|PermissionsGroup  $group
     */
    public function deleted(PermissionsGroup $group)
    {
        $this->event->dispatch(new PermissionsGroupEvents\DeletedPermissionsGroup($group));
    }
}
