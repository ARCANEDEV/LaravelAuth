<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachingRoleFromPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingRoleFromPermission
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedRoleFromPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  \Arcanesoft\Contracts\Auth\Models\Role        $role
     */
    public function __construct(Permission $permission, Role $role)
    {
        $this->permission = $permission;
        $this->role       = $role;
    }
}
