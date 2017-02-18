<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     AttachedPermissionToRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AttachedPermissionToRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AttachingPermissionToRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role        $role
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     */
    public function __construct(Role $role, Permission $permission)
    {
        $this->role       = $role;
        $this->permission = $permission;
    }
}
