<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Arcanedev\LaravelAuth\Events\Roles as RoleEvents;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     RoleObserver
 *
 * @package  Arcanedev\LaravelAuth\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleObserver extends AbstractObserver
{
    /* -----------------------------------------------------------------
     |  Events
     | -----------------------------------------------------------------
     */

    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function creating(Role $role)
    {
        $this->event->dispatch(new RoleEvents\CreatingRole($role));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function created(Role $role)
    {
        $this->event->dispatch(new RoleEvents\CreatedRole($role));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function updating(Role $role)
    {
        $this->event->dispatch(new RoleEvents\UpdatingRole($role));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function updated(Role $role)
    {
        $this->event->dispatch(new RoleEvents\UpdatedRole($role));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function saving(Role $role)
    {
        $this->event->dispatch(new RoleEvents\SavingRole($role));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function saved(Role $role)
    {
        $this->event->dispatch(new RoleEvents\SavedRole($role));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function deleting(Role $role)
    {
        $role->users()->detach();
        $role->permissions()->detach();

        $this->event->dispatch(new RoleEvents\DeletingRole($role));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|Role  $role
     */
    public function deleted(Role $role)
    {
        $this->event->dispatch(new RoleEvents\DeletedRole($role));
    }
}
