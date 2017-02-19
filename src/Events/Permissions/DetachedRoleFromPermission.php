<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedRoleFromPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedRoleFromPermission
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedRoleFromPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  \Arcanesoft\Contracts\Auth\Models\Role        $role
     * @param  int                                           $results
     */
    public function __construct(Permission $permission, Role $role, $results)
    {
        $this->permission = $permission;
        $this->role       = $role;
        $this->results    = $results;
    }
}
