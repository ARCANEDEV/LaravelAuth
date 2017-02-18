<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Events\RoleUser as RoleUserEvents;

/**
 * Class     RoleUserObserver
 *
 * @package  Arcanedev\LaravelAuth\Models\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleUserObserver extends AbstractObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Model Events
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Eloquent 'creating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function creating(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\CreatingRoleUser($roleUser));
    }

    /**
     * Eloquent 'created' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function created(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\CreatedRoleUser($roleUser));
    }

    /**
     * Eloquent 'updating' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function updating(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\UpdatingRoleUser($roleUser));
    }

    /**
     * Eloquent 'updated' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function updated(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\UpdatedRoleUser($roleUser));
    }

    /**
     * Eloquent 'saving' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function saving(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\SavingRoleUser($roleUser));
    }

    /**
     * Eloquent 'saved' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function saved(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\SavedRoleUser($roleUser));
    }

    /**
     * Eloquent 'deleting' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function deleting(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\DeletingRoleUser($roleUser));
    }

    /**
     * Eloquent 'deleted' event method.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function deleted(RoleUser $roleUser)
    {
        $this->event->dispatch(new RoleUserEvents\DeletedRoleUser($roleUser));
    }
}
