<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class     SyncedUserWithRoles
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SyncedUserWithRoles extends AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Illuminate\Database\Eloquent\Collection */
    public $roles;

    /** @var  array */
    public $synced;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * SyncingUserWithRoles constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User    $user
     * @param  \Illuminate\Database\Eloquent\Collection  $roles
     */
    public function __construct(User $user, Collection $roles, array $synced)
    {
        parent::__construct($user);

        $this->roles  = $roles;
        $this->synced = $synced;
    }
}
