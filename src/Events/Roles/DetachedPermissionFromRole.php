<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedPermissionFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedPermissionFromRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /** @var int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedPermissionFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role        $role
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  int                                           $results
     */
    public function __construct(Role $role, Permission $permission, $results)
    {
        $this->role       = $role;
        $this->permission = $permission;
        $this->results    = $results;
    }
}
