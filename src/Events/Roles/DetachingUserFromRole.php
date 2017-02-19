<?php namespace Arcanedev\LaravelAuth\Events\Roles;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachingUserFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingUserFromRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  \Arcanesoft\Contracts\Auth\Models\User|int */
    public $user;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachingUserFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role      $role
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $user
     */
    public function __construct(Role $role, $user)
    {
        $this->role = $role;
        $this->user = $user;
    }
}
