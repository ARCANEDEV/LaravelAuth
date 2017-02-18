<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;

/**
 * Class     AbstractRoleEvent
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractRoleEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * @var \Arcanesoft\Contracts\Auth\Models\Role|\Arcanedev\LaravelAuth\Models\Role
     */
    public $role;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * UserEvent constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|\Arcanedev\LaravelAuth\Models\Role  $role
     */
    public function __construct(RoleContract $role)
    {
        $this->role = $role;
    }
}
