<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Arcanedev\LaravelAuth\Events\Permissions as PermissionEvents;
use Arcanesoft\Contracts\Auth\Models\Permission;

/**
 * Class     PermissionObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionObserver extends AbstractObserver
{
    /* -----------------------------------------------------------------
     |  Events
     | -----------------------------------------------------------------
     */
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function creating(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\CreatingPermission($permission));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function created(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\CreatedPermission($permission));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function updating(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\UpdatingPermission($permission));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function updated(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\UpdatedPermission($permission));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function saving(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\SavingPermission($permission));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function saved(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\SavedPermission($permission));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function deleting(Permission $permission)
    {
        $permission->roles()->detach();

        $this->event->dispatch(new PermissionEvents\DeletingPermission($permission));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|Permission  $permission
     */
    public function deleted(Permission $permission)
    {
        $this->event->dispatch(new PermissionEvents\DeletedPermission($permission));
    }
}
