<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;
use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachedUserFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedUserFromRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  \Arcanesoft\Contracts\Auth\Models\User */
    public $user;

    /** @var  int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedUserFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role  $role
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     * @param  int                                     $results
     */
    public function __construct(Role $role, User $user, $results)
    {
        $this->role    = $role;
        $this->user    = $user;
        $this->results = $results;
    }
}
