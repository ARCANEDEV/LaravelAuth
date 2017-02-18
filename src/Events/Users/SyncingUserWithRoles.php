<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class     SyncingUserWithRoles
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SyncingUserWithRoles
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * @var \Arcanesoft\Contracts\Auth\Models\User
     */
    public $user;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $roles;

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
    public function __construct(User $user, Collection $roles)
    {
        $this->user  = $user;
        $this->roles = $roles;
    }
}
